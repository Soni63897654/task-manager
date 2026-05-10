<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\FileController;

/*
|--------------------------------------------------------------------------
| Public Routes (Guest)
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'index']);
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);


/*
|--------------------------------------------------------------------------
| Protected Routes (Only logged-in users)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [TaskController::class, 'dashboard'])->name('dashboard');
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/fetch-task-list', [TaskController::class, 'fetchTaskList']);
    Route::post('/tasks/store', [TaskController::class, 'store']);
    Route::post('/tasks/update', [TaskController::class, 'update']);
    Route::post('/tasks/delete', [TaskController::class, 'delete']);
    
    Route::get('/upload-files', [FileController::class, 'uploadFiles'])->name('files.index');
    Route::post('/upload-files', [FileController::class, 'uploadFilesPost'])->name('files.store');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});