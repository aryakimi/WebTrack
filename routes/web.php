<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ==========================================
// RUTE LOGIN & LOGOUT (Bisa diakses tanpa login)
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Jika buka web utama, langsung lempar ke login
Route::get('/', function () {
    return redirect('/login');
});

// ==========================================
// RUTE KHUSUS ADMIN (Wajib login sebagai Admin)
// ==========================================
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin', [ProjectController::class, 'dashboard'])->name('admin.dashboard');
    
    // Rute Proyek
    Route::get('/admin/projects', [ProjectController::class, 'indexProject'])->name('admin.projects');
    Route::post('/admin/projects/store', [ProjectController::class, 'storeProject'])->name('admin.projects.store');
    Route::delete('/admin/projects/{id}', [ProjectController::class, 'destroyProject'])->name('admin.projects.destroy'); // <-- RUTE BARU

    // Rute Tugas
    Route::get('/admin/tasks', [ProjectController::class, 'indexTask'])->name('admin.tasks');
    Route::post('/admin/tasks/store', [ProjectController::class, 'storeTask'])->name('admin.tasks.store');
    Route::get('/admin/all-tasks', [ProjectController::class, 'allTasks'])->name('admin.all-tasks');
    Route::delete('/admin/tasks/{id}', [ProjectController::class, 'destroyTask'])->name('admin.tasks.destroy');
    
    // Rute Tim
    Route::get('/admin/team', [ProjectController::class, 'indexTeam'])->name('admin.team');
    Route::post('/admin/team/store', [ProjectController::class, 'storeTeamMember'])->name('admin.team.store');
});

// ==========================================
// RUTE KHUSUS MEMBER (Wajib login sebagai Team Member)
// ==========================================
Route::middleware(['auth:member'])->group(function () {
    Route::get('/member', [ProjectController::class, 'memberTasks'])->name('member.dashboard');
    Route::post('/member/tasks/update/{id}', [ProjectController::class, 'updateTaskStatus'])->name('member.tasks.update');

    Route::post('/member/tasks/update-kanban/{id}', [ProjectController::class, 'updateKanbanStatus'])->name('member.tasks.kanban');
});
