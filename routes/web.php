<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Route::get('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
Route::get('/oauth_callback/login', [App\Http\Controllers\OAuthCallbackController::class, 'login']);
Route::get('/oauth_callback/profile', [App\Http\Controllers\OAuthCallbackController::class, 'profile']);

Route::get('presentation', [App\Http\Controllers\PresentationController::class, 'index']);
Route::get('reglament', [App\Http\Controllers\ReglamentController::class, 'index']);
Route::get('results', [App\Http\Controllers\ResultsController::class, 'index']);

Route::resource('projects', App\Http\Controllers\ProjectsController::class);

Route::middleware(['role:admin'])->group(function() {
    Route::resource('schools', App\Http\Controllers\SchoolsController::class);
    Route::resource('users', App\Http\Controllers\UsersController::class)->only(['index', 'edit', 'update', 'destroy']);
});