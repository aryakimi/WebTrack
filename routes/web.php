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
    Route::get('/admin/projects', [ProjectController::class, 'indexProject'])->name('admin.projects');
    Route::post('/admin/projects/store', [ProjectController::class, 'storeProject'])->name('admin.projects.store');
    Route::get('/admin/tasks', [ProjectController::class, 'indexTask'])->name('admin.tasks');
    Route::post('/admin/tasks/store', [ProjectController::class, 'storeTask'])->name('admin.tasks.store');
    Route::get('/admin/all-tasks', [ProjectController::class, 'allTasks'])->name('admin.all-tasks');
    Route::get('/admin/team', [ProjectController::class, 'indexTeam'])->name('admin.team');
    Route::post('/admin/team/store', [ProjectController::class, 'storeTeamMember'])->name('admin.team.store');
    // RUTE BARU UNTUK HAPUS TUGAS (Biar bisa ngehapus data ngasal tadi)
    Route::delete('/admin/tasks/{id}', [ProjectController::class, 'destroyTask'])->name('admin.tasks.destroy');
});

// ==========================================
// RUTE KHUSUS MEMBER (Wajib login sebagai Team Member)
// ==========================================
Route::middleware(['auth:member'])->group(function () {
    Route::get('/member', [ProjectController::class, 'memberTasks'])->name('member.dashboard');
    
    // RUTE BARU UNTUK UPDATE STATUS TUGAS DARI MEMBER
    Route::post('/member/tasks/update/{id}', [ProjectController::class, 'updateTaskStatus'])->name('member.tasks.update');
    
});