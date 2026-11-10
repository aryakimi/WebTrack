<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Auth; 

class ProjectController extends Controller
{
    // FUNGSI BANTUAN: Hitung Persentase Progres Otomatis
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
    $recentActivities = Task::with('member')->latest('updated_at')->take(5)->get();

    // HITUNG TUGAS TERLAMBAT
    $lateTasks = Task::where('deadline', '<', now())
                     ->where('status', '!=', 'Done')
                     ->count();

    foreach($activeProjects as $p) {
        $p->progress = $this->calculateProgress($p->id_project);
    }
    
    // Kirim $lateTasks
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

    // 4. Halaman Pembagian Tugas (Admin)
    public function indexTask()
    {
        $tasks = Task::with(['project', 'member'])->get(); 
        $projects = Project::all();
        $members = TeamMember::all();
        
        return view('admin.tasks', compact('tasks', 'projects', 'members'));
    }

    // 5. Simpan Tugas Baru
    public function storeTask(Request $request)
    {
        Task::create([
            'id_project' => $request->id_project,
            'id_member' => $request->id_member,
            'nama_task' => $request->nama_task,
            'deadline' => $request->deadline,
            'status' => 'To Do',
            'bobot' => 10, 
            'prasyarat' => $request->prasyarat, 
        ]);

        return redirect()->route('admin.tasks')->with('success', 'Tugas berhasil di-assign!');
    }

    // 6. Halaman Tugas Saya (Team Member)
    public function memberTasks()
    {
        $memberId = Auth::guard('member')->user()->id_member; 
        $myTasks = Task::where('id_member', $memberId)->get();
        return view('member.dashboard', compact('myTasks'));
    }

    // 7. Update Status
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

        return redirect()->back()->with('success', 'Status tugas berhasil diupdate!');
    }

    // 8. Hapus Tugas
    public function destroyTask($id)
    {
        Task::findOrFail($id)->delete();
        return redirect()->route('admin.tasks')->with('success', 'Tugas berhasil dihapus!');
    }
    
    // 9. Halaman Daftar Tugas
    public function allTasks()
    {
        $tasks = Task::with(['project', 'member'])->get(); 
        return view('admin.all-tasks', compact('tasks'));
    }

    // 10. Halaman Anggota Tim
    public function indexTeam()
    {
        $members = TeamMember::all(); 
        return view('admin.team', compact('members'));
    }

    // 11. Simpan Anggota Tim Baru (INI YANG BARU DITAMBAHKAN)
    public function storeTeamMember(Request $request)
    {
        $request->validate([
            'nama_member' => 'required',
            'email' => 'required|email|unique:team_members,email', 
            'posisi' => 'required', // Ini bakal nangkep value "Frontend", "Backend", dll
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
}