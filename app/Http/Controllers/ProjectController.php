<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\TeamMember;
use App\Models\Client; 
use Illuminate\Support\Facades\Auth; 

class ProjectController extends Controller
{
    // ==============================================================
    // FUNGSI BANTUAN (HELPERS)
    // ==============================================================

    // Hitung Persentase Progres Proyek Otomatis
    private function calculateProgress($projectId)
    {
        $tasks = Task::where('id_project', $projectId)->get();
        $totalBobot = $tasks->sum('bobot');
        
        if ($totalBobot == 0) return 0; 

        $bobotTercapai = 0;

        foreach ($tasks as $task) {
            $multiplier = match($task->status) {
                'Done' => 1.0,        // 100%
                'Review' => 0.8,      // 80%
                'In Progress' => 0.5, // 50%
                'To Do', 'Todo' => 0, // 0%
                default => 0
            };
            
            $bobotTask = $task->bobot ?? 10; 
            $bobotTercapai += ($bobotTask * $multiplier);
        }

        return round(($bobotTercapai / $totalBobot) * 100);
    }

    // Hitung Workload (Beban Kerja) Anggota
    private function calculateWorkload($memberId)
    {
        $activeTasks = Task::where('id_member', $memberId)
                           ->where('status', '!=', 'Done')
                           ->get();
        
        $totalRemainingBobot = 0;
        foreach($activeTasks as $t) {
            $multiplier = match($t->status) {
                'Review' => 0.2,      
                'In Progress' => 0.5, 
                'To Do', 'Todo' => 1.0, 
                default => 1.0
            };
            $bobot = $t->bobot ?? 10; 
            $totalRemainingBobot += ($bobot * $multiplier);
        }

        $maxBobot = 40; // Asumsi 40 bobot = 100% Workload
        return min(100, round(($totalRemainingBobot / $maxBobot) * 100));
    }

    // FUNGSI BARU: Cek Validasi Prasyarat (Task Dependency)
    private function isTaskLocked($task)
    {
        // Tugas yang udah 'Done' TIDAK BOLEH dikunci, biar bisa di-drag balik buat revisi
        if ($task->status === 'Done' || !$task->prasyarat) {
            return false;
        }

        // Cari prasyarat HANYA di dalam proyek yang sama
        $prasTask = Task::where('id_project', $task->id_project)
                        ->where('nama_task', $task->prasyarat)
                        ->first();

        // Kalau prasyarat ada dan belum 'Done', berarti terkunci (True)
        return ($prasTask && $prasTask->status !== 'Done');
    }


    // ==============================================================
    // FITUR ADMIN: DASHBOARD
    // ==============================================================

    public function dashboard()
    {
        $totalProjects = Project::count();
        $totalTasks = Task::count();
        $totalMembers = TeamMember::count();
        
        $recentTasks = Task::with(['project', 'member'])->latest()->take(5)->get();
        $activeProjects = Project::latest()->take(3)->get();
        
        $assignments = Task::with(['project', 'member'])->latest('created_at')->take(3)->get()->map(function($item) {
            $item->log_type = 'assign'; 
            $item->log_time = $item->created_at;
            return $item;
        });

        $completions = Task::with(['project', 'member'])->where('status', 'Done')->latest('updated_at')->take(3)->get()->map(function($item) {
            $item->log_type = 'complete'; 
            $item->log_time = $item->updated_at;
            return $item;
        });

        $recentActivities = $assignments->concat($completions)->sortByDesc('log_time')->take(5);
        $lateTasks = Task::where('deadline', '<', now())->where('status', '!=', 'Done')->count();

        foreach($activeProjects as $p) {
            $p->progress = $this->calculateProgress($p->id_project);
        }
        
        return view('admin.dashboard', compact(
            'totalProjects', 'totalTasks', 'totalMembers', 
            'recentTasks', 'activeProjects', 'recentActivities', 'lateTasks'
        ));
    }


    // ==============================================================
    // FITUR ADMIN: PROYEK
    // ==============================================================

    public function indexProject()
    {
        $projects = Project::all();
        $clients = Client::all(); 
        
        foreach($projects as $p) {
            $p->progress = $this->calculateProgress($p->id_project);
        }
        
        $totalProjects = $projects->count();
        $doneProjects = $projects->where('status', 'Done')->count();
        $activeProjects = $projects->where('status', '!=', 'Done')->count();
        
        return view('admin.projects', compact('projects', 'clients', 'totalProjects', 'doneProjects', 'activeProjects'));
    }

    public function storeProject(Request $request)
    {
        $request->validate([
            'nama_project' => 'required',
            'deadline' => 'required|date',
            'nama_client' => 'required', 
        ]);

        $client = Client::firstOrCreate(
            ['nama_client' => $request->nama_client],
            [
                'email' => $request->email_client,
                'no_telepon' => $request->no_telepon_client,
                'alamat' => $request->alamat_client,
            ]
        );

        Project::create([
            'id_admin' => 1, 
            'id_client' => $client->id_client, 
            'nama_project' => $request->nama_project,
            'deskripsi' => $request->deskripsi,
            'tanggal_mulai' => now(),
            'deadline' => $request->deadline,
            'status' => 'Pending',
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Proyek dan Client berhasil ditambahkan!');
    }

    public function destroyProject($id)
    {
        $project = Project::findOrFail($id);
        $id_client = $project->id_client; 

        Task::where('id_project', $id)->delete();
        $project->delete();

        $cekProyekLain = Project::where('id_client', $id_client)->count();
        if ($cekProyekLain == 0) {
            Client::where('id_client', $id_client)->delete();
        }

        return redirect()->route('admin.projects')->with('success', 'Proyek, tugas, dan Client terkait berhasil dihapus!');
    }


    // ==============================================================
    // FITUR ADMIN: CLIENT
    // ==============================================================

    public function indexClient()
    {
        $clients = Client::all();
        $totalClients = $clients->count();

        foreach($clients as $c) {
            $c->total_projects = Project::where('id_client', $c->id_client)->count();
            $firstProject = Project::where('id_client', $c->id_client)->orderBy('tanggal_mulai', 'asc')->first();
            $c->tanggal_mesen = $firstProject ? $firstProject->tanggal_mulai : null;
        }

        return view('admin.clients', compact('clients', 'totalClients'));
    }

    public function destroyClient($id)
    {
        $client = Client::findOrFail($id);
        $projectCount = Project::where('id_client', $id)->count();
        
        if ($projectCount > 0) {
            return redirect()->back()->with('error', 'Gagal! Client ini masih memiliki proyek. Hapus semua proyeknya terlebih dahulu.');
        }

        $client->delete();
        return redirect()->back()->with('success', 'Data Client berhasil dihapus!');
    }


    // ==============================================================
    // FITUR ADMIN: TUGAS
    // ==============================================================

    public function indexTask()
    {
        $tasks = Task::with(['project', 'member'])->get(); 
        $projects = Project::all();
        $members = TeamMember::all();
        
        foreach($members as $m) {
            $m->workload = $this->calculateWorkload($m->id_member);
        }
        
        return view('admin.tasks', compact('tasks', 'projects', 'members'));
    }

    public function storeTask(Request $request)
    {
        $project = Project::findOrFail($request->id_project);

        Task::create([
            'id_project' => $request->id_project,
            'id_member' => $request->id_member,
            'nama_task' => $request->nama_task,
            'deadline' => $project->deadline, 
            'status' => 'To Do',
            'bobot' => 10, 
            'prasyarat' => $request->prasyarat, 
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Tugas berhasil di-assign!');
    }

    public function allTasks()
    {
        $tasks = Task::with(['project', 'member'])->get(); 
        return view('admin.all-tasks', compact('tasks'));
    }

    public function destroyTask($id)
    {
        Task::findOrFail($id)->delete();
        return redirect()->route('admin.tasks')->with('success', 'Tugas berhasil dihapus!');
    }


    // ==============================================================
    // FITUR ADMIN: TIM & LAPORAN
    // ==============================================================

    public function indexTeam()
    {
        $members = TeamMember::all(); 
        
        foreach($members as $m) {
            $m->workload = $this->calculateWorkload($m->id_member);
        }

        return view('admin.team', compact('members'));
    }

    public function storeTeamMember(Request $request)
    {
        $request->validate([
            'nama_member' => 'required',
            'email' => 'required|email|unique:team_members,email', 
            'posisi' => 'required', 
            'password' => 'required|min:6',
        ]);

        TeamMember::create([
            'nama_member' => $request->nama_member,
            'email' => $request->email,
            'password' => bcrypt($request->password), 
            'posisi' => $request->posisi,
            'status' => 'Aktif',
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Anggota tim berhasil ditambahkan!');
    }

    public function indexReport()
    {
        $totalProjects = Project::count();
        $doneProjects = Project::where('status', 'Done')->count();
        $onProgressProjects = Project::where('status', 'On Progress')->count();
        
        $totalTasks = Task::count();
        $doneTasks = Task::where('status', 'Done')->count();
        $reviewTasks = Task::where('status', 'Review')->count();
        $inProgressTasks = Task::where('status', 'In Progress')->count();
        $todoTasks = Task::whereIn('status', ['To Do', 'Todo'])->count();

        $teamPerformance = TeamMember::all()->map(function($member) {
            $member->total_tasks = Task::where('id_member', $member->id_member)->count();
            $member->done_tasks = Task::where('id_member', $member->id_member)->where('status', 'Done')->count();
            $member->pending_tasks = $member->total_tasks - $member->done_tasks;
            
            $member->completion_rate = $member->total_tasks > 0 
                ? round(($member->done_tasks / $member->total_tasks) * 100) 
                : 0;
                
            return $member;
        })->sortByDesc('done_tasks');

        return view('admin.reports', compact(
            'totalProjects', 'doneProjects', 'onProgressProjects',
            'totalTasks', 'doneTasks', 'reviewTasks', 'inProgressTasks', 'todoTasks',
            'teamPerformance'
        ));
    }


    // ==============================================================
    // FITUR MEMBER: TUGAS, PROYEK, REKAN TIM
    // ==============================================================

    public function memberTasks()
    {
        $memberId = Auth::guard('member')->user()->id_member; 
        $myTasks = Task::where('id_member', $memberId)->get();

        $totalTasks = $myTasks->count();
        $doneTasks = $myTasks->where('status', 'Done')->count();
        $pendingTasks = $totalTasks - $doneTasks;
        $overdueTasks = Task::where('id_member', $memberId)
                            ->where('deadline', '<', now())
                            ->where('status', '!=', 'Done')
                            ->count();

        $myProjectIds = Task::where('id_member', $memberId)->pluck('id_project')->unique();
        
        $recentActivities = Task::with(['member', 'project'])
            ->whereIn('id_project', $myProjectIds)
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('member.dashboard', compact(
            'myTasks', 'totalTasks', 'doneTasks', 'pendingTasks', 'overdueTasks', 'recentActivities'
        ));
    }

    public function memberProjects()
    {
        $memberId = Auth::guard('member')->user()->id_member;
        $projectIds = Task::where('id_member', $memberId)->pluck('id_project')->unique();
        $projects = Project::whereIn('id_project', $projectIds)->get();
        
        foreach($projects as $p) {
            $p->progress = $this->calculateProgress($p->id_project);
            $p->my_total_tasks = Task::where('id_project', $p->id_project)->where('id_member', $memberId)->count();
            $p->my_done_tasks = Task::where('id_project', $p->id_project)->where('id_member', $memberId)->where('status', 'Done')->count();
        }

        return view('member.projects', compact('projects'));
    }

    public function memberTeam()
    {
        $members = TeamMember::all();
        
        foreach($members as $m) {
            $m->workload = $this->calculateWorkload($m->id_member);
        }

        return view('member.team', compact('members'));
    }

    // UPDATE: Tambah validasi prasyarat
    public function updateTaskStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // Validasi Prasyarat (Anti Bypass)
        if ($this->isTaskLocked($task)) {
            return redirect()->back()->with('error', 'Gagal! Selesaikan prasyarat tugas ini terlebih dahulu.');
        }

        $task->status = $request->status;
        $task->save();

        $project = $task->project;
        if ($project) {
            $progress = $this->calculateProgress($project->id_project);
            $project->status = ($progress == 100) ? 'Done' : 'On Progress';
            $project->save();
        }

        return redirect()->back()->with('success', 'Status tugas berhasil diupdate!');
    }

    // UPDATE: Tambah validasi prasyarat & response JSON
    public function updateKanbanStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // Validasi Prasyarat (Anti Bypass)
        if ($this->isTaskLocked($task)) {
            return response()->json([
                'success' => false, 
                'message' => 'Tugas ini masih terkunci! Selesaikan prasyaratnya terlebih dahulu.'
            ], 403);
        }

        $task->status = $request->status;
        $task->save();

        $project = $task->project;
        if ($project) {
            $progress = $this->calculateProgress($project->id_project);
            $project->status = ($progress == 100) ? 'Done' : 'On Progress';
            $project->save();
        }

        return response()->json(['success' => true]);
    }

    public function memberCalendar()
    {
        $memberId = Auth::guard('member')->user()->id_member;
        $myTasks = Task::where('id_member', $memberId)->get();

        $events = $myTasks->map(function($task) {
            return [
                'title' => $task->nama_task,
                'start' => $task->deadline,
                'backgroundColor' => $task->status == 'Done' ? '#4ade80' : '#ff6b00',
                'borderColor' => $task->status == 'Done' ? '#4ade80' : '#ff6b00',
                'textColor' => '#000'
            ];
        });

        return view('member.calendar', compact('events'));
    }
    

    // ==============================================================
    // FITUR BERSAMA: PROFIL (ADMIN & MEMBER)
    // ==============================================================

    public function editProfile()
    {
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $role = 'admin';
        } else {
            $user = Auth::guard('member')->user();
            $role = 'member';
        }

        return view('profile', compact('user', 'role'));
    }

    public function updateProfile(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $table = 'admins';
            $id_col = 'id_admin';
            $name_col = 'nama';
        } else {
            $user = Auth::guard('member')->user();
            $table = 'team_members';
            $id_col = 'id_member';
            $name_col = 'nama_member';
        }

        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:'.$table.',email,'.$user->$id_col.','.$id_col,
            'password' => 'nullable|min:6',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user->$name_col = $request->nama;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto && \Storage::exists('public/' . $user->foto)) {
                \Storage::delete('public/' . $user->foto);
            }
            $path = $request->file('foto')->store('profile_photos', 'public');
            $user->foto = $path;
        }

        $user->save();

        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard')->with('success', 'Profil berhasil diperbarui!');
        } else {
            return redirect()->route('member.dashboard')->with('success', 'Profil berhasil diperbarui!');
        }
    }

    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
        } else {
            $user = Auth::guard('member')->user();
        }

        if ($request->hasFile('foto')) {
            if ($user->foto && \Storage::exists('public/' . $user->foto)) {
                \Storage::delete('public/' . $user->foto);
            }
            $path = $request->file('foto')->store('profile_photos', 'public');
            $user->foto = $path;
            $user->save();
        }

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui!');
    }
}