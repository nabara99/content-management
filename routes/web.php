<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CaptchaController;
use App\Livewire\Admin\InstanceManager;
use App\Livewire\Admin\ContentManager as AdminContentManager;
use App\Livewire\Admin\TemplateManager;
use App\Livewire\Admin\UserManager;
use App\Livewire\User\ContentManager;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Captcha
Route::get('/captcha', [CaptchaController::class, 'generate']);

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Logout
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', UserManager::class)->name('users.index');
    Route::get('/instances', InstanceManager::class)->name('instances.index');
    Route::get('/templates', TemplateManager::class)->name('templates.index');
    Route::get('/contents', AdminContentManager::class)->name('contents.index');
});

// User routes
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\User\UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/contents', ContentManager::class)->name('contents.index');
});
