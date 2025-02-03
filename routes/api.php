<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUserAuth;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

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
    });
});

