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
    
    // Proyek
    Route::get('/admin/projects', [ProjectController::class, 'indexProject'])->name('admin.projects');
    Route::post('/admin/projects/store', [ProjectController::class, 'storeProject'])->name('admin.projects.store');
    Route::delete('/admin/projects/{id}', [ProjectController::class, 'destroyProject'])->name('admin.projects.destroy');

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
    Route::post('/admin/profile/photo', [ProjectController::class, 'updateFoto'])->name('admin.profile.photo');
    Route::post('/member/profile/photo', [ProjectController::class, 'updateFoto'])->name('member.profile.photo');
});

// ==========================================
// RUTE KHUSUS MEMBER
// ==========================================
Route::middleware(['auth:member'])->group(function () {
    Route::get('/member', [ProjectController::class, 'memberTasks'])->name('member.dashboard');
    Route::post('/member/tasks/update/{id}', [ProjectController::class, 'updateTaskStatus'])->name('member.tasks.update');
    Route::post('/member/tasks/update-kanban/{id}', [ProjectController::class, 'updateKanbanStatus'])->name('member.tasks.kanban');

    // Edit Profil Member
    Route::get('/member/profile', [ProjectController::class, 'editProfile'])->name('member.profile');
    Route::post('/member/profile/update', [ProjectController::class, 'updateProfile'])->name('member.profile.update');
});