<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUserAuth;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('historico', [HistoryController::class, 'index']);
Route::post('analisis', [HistoryController::class, 'analizarDatos']);

// Private routes
Route::middleware([IsUserAuth::class])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('me', [AuthController::class, 'getUser']);
    Route::get('posts', [PostController::class, 'index']);

    // Only admin
    Route::middleware([IsAdmin::class])->group(function () {
        Route::post('posts', [PostController::class, 'addPost']);
        Route::get('/posts/{id}', [PostController::class, 'getPost']);
        Route::put('/posts/{id}', [PostController::class, 'updatePost']);
        Route::delete('/posts/{id}', [PostController::class, 'deletePost']);
        Route::get('users', [AuthController::class, 'index']);
        Route::put('/users/{id}', [AuthController::class, 'updateUser']);
    });
});

