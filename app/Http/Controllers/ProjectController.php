<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Auth; 

class ProjectController extends Controller
{
    // FUNGSI BANTUAN: Hitung Persentase Progres Proyek Otomatis
    private function calculateProgress($projectId)
    {
        $totalTasks = Task::where('id_project', $projectId)->count();
        if ($totalTasks == 0) return 0; 
        
        $doneTasks = Task::where('id_project', $projectId)->where('status', 'Done')->count();
        return round(($doneTasks / $totalTasks) * 100);
    }

    // 1. Halaman Dashboard Admin
    public function dashboard()
    {
        $totalProjects = Project::count();
        $totalTasks = Task::count();
        $totalMembers = TeamMember::count();
        
        $recentTasks = Task::with(['project', 'member'])->latest()->take(5)->get();
        $activeProjects = Project::latest()->take(3)->get();
        
        // --- LOGIKA AKTIVITAS TERBARU ---
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
        // ---------------------------------

        $lateTasks = Task::where('deadline', '<', now())->where('status', '!=', 'Done')->count();

        foreach($activeProjects as $p) {
            $p->progress = $this->calculateProgress($p->id_project);
        }
        
        return view('admin.dashboard', compact('totalProjects', 'totalTasks', 'totalMembers', 'recentTasks', 'activeProjects', 'recentActivities', 'lateTasks'));
    }

    // 2. Halaman Daftar Proyek
    public function indexProject()
    {
        $projects = Project::all();
        
        foreach($projects as $p) {
            $p->progress = $this->calculateProgress($p->id_project);
        }
        
        $totalProjects = $projects->count();
        $doneProjects = $projects->where('status', 'Done')->count();
        $activeProjects = $projects->where('status', '!=', 'Done')->count();
        
        return view('admin.projects', compact('projects', 'totalProjects', 'doneProjects', 'activeProjects'));
    }

    // 3. Simpan Proyek Baru
    public function storeProject(Request $request)
    {
        $request->validate([
            'nama_project' => 'required',
            'deadline' => 'required|date',
        ]);

        Project::create([
            'id_admin' => 1, 
            'id_client' => 1, 
            'nama_project' => $request->nama_project,
            'deskripsi' => $request->deskripsi,
            'tanggal_mulai' => now(),
            'deadline' => $request->deadline,
            'status' => 'Pending',
        ]);

        return redirect()->route('admin.projects')->with('success', 'Proyek berhasil ditambahkan!');
    }

    // 4. Hapus Proyek
    public function destroyProject($id)
    {
        $project = Project::findOrFail($id);
        Task::where('id_project', $id)->delete();
        $project->delete();
        return redirect()->route('admin.projects')->with('success', 'Proyek dan tugas terkait berhasil dihapus!');
    }

    // 5. Halaman Pembagian Tugas (Admin)
    public function indexTask()
    {
        $tasks = Task::with(['project', 'member'])->get(); 
        $projects = Project::all();
        $members = TeamMember::all();
        
        // Hitung workload secara Real-Time untuk setiap anggota
        foreach($members as $m) {
            $activeTasks = Task::where('id_member', $m->id_member)
                                ->where('status', '!=', 'Done')
                                ->count();
            $m->workload = min(100, $activeTasks * 25); 
        }
        
        return view('admin.tasks', compact('tasks', 'projects', 'members'));
    }

    // 6. Simpan Tugas Baru (Otomatis Ambil Deadline Proyek)
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

        return redirect()->route('admin.tasks')->with('success', 'Tugas berhasil di-assign!');
    }

    // 7. Halaman Tugas Saya (Team Member)
    public function memberTasks()
    {
        $memberId = Auth::guard('member')->user()->id_member; 
        $myTasks = Task::where('id_member', $memberId)->get();
        return view('member.dashboard', compact('myTasks'));
    }

    // 8. Update Status Tugas
    public function updateTaskStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->status = $request->status;
        $task->save();

        $project = $task->project;
        if ($project) {
            $progress = $this->calculateProgress($project->id_project);
            
            if ($progress == 100) {
                $project->status = 'Done';
            } else {
                $project->status = 'On Progress';
            }
            $project->save();
        }

        // KIRIM NOTIFIKASI SUKSES
        return redirect()->back()->with('success', 'Status tugas berhasil diupdate!');
    }

    // 9. Hapus Tugas
    public function destroyTask($id)
    {
        Task::findOrFail($id)->delete();
        return redirect()->route('admin.tasks')->with('success', 'Tugas berhasil dihapus!');
    }
    
    // 10. Halaman Daftar Semua Tugas
    public function allTasks()
    {
        $tasks = Task::with(['project', 'member'])->get(); 
        return view('admin.all-tasks', compact('tasks'));
    }

    // 11. Halaman Anggota Tim
    public function indexTeam()
    {
        $members = TeamMember::all(); 
        
        foreach($members as $m) {
            $activeTasks = Task::where('id_member', $m->id_member)
                                ->where('status', '!=', 'Done')
                                ->count();
            $m->workload = min(100, $activeTasks * 25); 
        }

        return view('admin.team', compact('members'));
    }

    // 12. Simpan Anggota Tim Baru
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

        return redirect()->route('admin.team')->with('success', 'Anggota tim berhasil ditambahkan!');
    }

    // FUNGSI BARU: Update Status via Drag & Drop Kanban
    public function updateKanbanStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->status = $request->status;
        $task->save();

        // Update progres proyeknya juga (sama kayak fungsi sebelumnya)
        $project = $task->project;
        if ($project) {
            $progress = $this->calculateProgress($project->id_project);
            $project->status = ($progress == 100) ? 'Done' : 'On Progress';
            $project->save();
        }

        return response()->json(['success' => true, 'message' => 'Status berhasil diubah!']);
    }
}