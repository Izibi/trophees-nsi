<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Route::get('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
Route::get('/oauth_callback/login', [App\Http\Controllers\OAuthCallbackController::class, 'login']);
Route::get('/oauth_callback/profile', [App\Http\Controllers\OAuthCallbackController::class, 'profile']);

Route::middleware('auth')->group(function() {
    Route::get('presentation', [App\Http\Controllers\PresentationController::class, 'index']);
    Route::get('reglament', [App\Http\Controllers\ReglamentController::class, 'index']);
    Route::get('results', [App\Http\Controllers\ResultsController::class, 'index']);

    Route::resource('projects', App\Http\Controllers\ProjectsController::class);

    Route::middleware(['role:jury'])->group(function() {
        Route::post('projects/{project}/set_rating', [App\Http\Controllers\ProjectsController::class, 'setRating'])->name('projects.set_rating');
    });

    Route::middleware(['role:admin'])->group(function() {
        Route::resource('schools', App\Http\Controllers\SchoolsController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::post('schools/{school}/hide', [App\Http\Controllers\SchoolsController::class, 'hide'])->name('schools.hide');
        Route::resource('users', App\Http\Controllers\UsersController::class)->only(['index', 'edit', 'update', 'destroy']);
    });

    Route::get('user_schools/search', [App\Http\Controllers\UserSchoolsController::class, 'search']);
    Route::post('user_schools/add', [App\Http\Controllers\UserSchoolsController::class, 'add']);
    Route::post('user_schools/create', [App\Http\Controllers\UserSchoolsController::class, 'create']);
    Route::post('user_schools/remove', [App\Http\Controllers\UserSchoolsController::class, 'remove']);
});