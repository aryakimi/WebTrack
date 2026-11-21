<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ==========================================
// RUTE LOGIN & LOGOUT
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect('/login');
});

// ==========================================
// RUTE KHUSUS ADMIN
// ==========================================
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin', [ProjectController::class, 'dashboard'])->name('admin.dashboard');
    
    // Proyek (Termasuk Client di dalamnya)
    Route::get('/admin/projects', [ProjectController::class, 'indexProject'])->name('admin.projects');
    Route::post('/admin/projects/store', [ProjectController::class, 'storeProject'])->name('admin.projects.store');
    Route::delete('/admin/projects/{id_project}', [ProjectController::class, 'destroyProject'])->name('admin.projects.destroy');

    // Tugas
    Route::get('/admin/tasks', [ProjectController::class, 'indexTask'])->name('admin.tasks');
    Route::post('/admin/tasks/store', [ProjectController::class, 'storeTask'])->name('admin.tasks.store');
    Route::get('/admin/all-tasks', [ProjectController::class, 'allTasks'])->name('admin.all-tasks');
    Route::delete('/admin/tasks/{id}', [ProjectController::class, 'destroyTask'])->name('admin.tasks.destroy');
    
    // Tim
    Route::get('/admin/team', [ProjectController::class, 'indexTeam'])->name('admin.team');
    Route::post('/admin/team/store', [ProjectController::class, 'storeTeamMember'])->name('admin.team.store');

    // Edit Profil Admin
    Route::get('/admin/profile', [ProjectController::class, 'editProfile'])->name('admin.profile');
    Route::post('/admin/profile/update', [ProjectController::class, 'updateProfile'])->name('admin.profile.update');

    // Client
    Route::get('/admin/clients', [ProjectController::class, 'indexClient'])->name('admin.clients');
    Route::delete('/admin/clients/{id_client}', [ProjectController::class, 'destroyClient'])->name('admin.clients.destroy');

    // Laporan
    Route::get('/admin/reports', [ProjectController::class, 'indexReport'])->name('admin.reports');
});

// ==========================================
// RUTE KHUSUS MEMBER
// ==========================================
Route::middleware(['auth:member'])->group(function () {
    // Tugas Saya (Dashboard Member)
    Route::get('/member', [ProjectController::class, 'memberTasks'])->name('member.dashboard');
    Route::post('/member/tasks/update/{id}', [ProjectController::class, 'updateTaskStatus'])->name('member.tasks.update');
    Route::post('/member/tasks/update-kanban/{id}', [ProjectController::class, 'updateKanbanStatus'])->name('member.tasks.kanban');

    // Proyek Saya (BARU)
    Route::get('/member/projects', [ProjectController::class, 'memberProjects'])->name('member.projects');

    // Rekan Tim (BARU)
    Route::get('/member/team', [ProjectController::class, 'memberTeam'])->name('member.team');

    // Edit Profil Member
    Route::get('/member/profile', [ProjectController::class, 'editProfile'])->name('member.profile');
    Route::post('/member/profile/update', [ProjectController::class, 'updateProfile'])->name('member.profile.update');

    // Kalender Deadline
    Route::get('/member/calendar', [ProjectController::class, 'memberCalendar'])->name('member.calendar');
});