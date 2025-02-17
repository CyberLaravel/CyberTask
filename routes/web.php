<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskAssignmentController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});


Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);
    Route::resource('tasks.comments', CommentController::class)->shallow();
    Route::resource('tasks.attachments', AttachmentController::class)->shallow();
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])
        ->name('attachments.download');

    Route::post('tasks/{task}/dependencies', [TaskController::class, 'addDependency'])
        ->name('tasks.dependencies.store');
    Route::delete('tasks/{task}/dependencies/{dependency}', [TaskController::class, 'removeDependency'])
        ->name('tasks.dependencies.destroy');

    Route::post('tasks/{task}/assignments', [TaskAssignmentController::class, 'store'])
        ->name('tasks.assignments.store');
    Route::delete('tasks/{task}/assignments/{user}', [TaskAssignmentController::class, 'destroy'])
        ->name('tasks.assignments.destroy');

    Route::resource('projects', ProjectController::class);
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
});

Route::get('/', function () {
    return redirect()->route('tasks.index');
});
