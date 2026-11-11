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
        
        // Logika Aktivitas Terbaru
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

    // 3. Simpan Proyek Baru (LANGSUNG KE DASHBOARD)
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

        return redirect()->route('admin.dashboard')->with('success', 'Proyek berhasil ditambahkan!');
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
        
        foreach($members as $m) {
            $activeTasks = Task::where('id_member', $m->id_member)
                                ->where('status', '!=', 'Done')
                                ->count();
            $m->workload = min(100, $activeTasks * 25); 
        }
        
        return view('admin.tasks', compact('tasks', 'projects', 'members'));
    }

    // 6. Simpan Tugas Baru (LANGSUNG KE DASHBOARD)
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
            $project->status = ($progress == 100) ? 'Done' : 'On Progress';
            $project->save();
        }

        return redirect()->back()->with('success', 'Status tugas berhasil diupdate!');
    }

    // 9. Update Kanban (AJAX)
    public function updateKanbanStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);
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

    // 10. Hapus Tugas
    public function destroyTask($id)
    {
        Task::findOrFail($id)->delete();
        return redirect()->route('admin.tasks')->with('success', 'Tugas berhasil dihapus!');
    }
    
    // 11. Halaman Daftar Semua Tugas
    public function allTasks()
    {
        $tasks = Task::with(['project', 'member'])->get(); 
        return view('admin.all-tasks', compact('tasks'));
    }

    // 12. Halaman Anggota Tim
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

    // 13. Simpan Anggota Tim Baru (LANGSUNG KE DASHBOARD)
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

    // 14. Halaman Edit Profil (Terpisah)
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

    // 15. Simpan Perubahan Profil
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
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi Foto
        ]);

        // Update Data Dasar
        $user->$name_col = $request->nama;
        $user->email = $request->email;

        // Update Password (kalau diisi)
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // Update Foto (kalau ada file yang di-upload)
        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($user->foto && \Storage::exists('public/' . $user->foto)) {
                \Storage::delete('public/' . $user->foto);
            }
            // Simpan foto baru
            $path = $request->file('foto')->store('profile_photos', 'public');
            $user->foto = $path;
        }

        $user->save();

       // Jika yang login Admin, lempar ke Dashboard Admin. Jika Member, lempar ke Tugas Saya.
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard')->with('success', 'Profil berhasil diperbarui!');
        } else {
            return redirect()->route('member.dashboard')->with('success', 'Profil berhasil diperbarui!');
        }
    }
    // 16. Upload Foto Profil
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