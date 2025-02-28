<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Route::get('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
Route::get('/profile', [App\Http\Controllers\AuthController::class, 'profile']);
Route::get('/oauth_callback/login', [App\Http\Controllers\OAuthCallbackController::class, 'login']);
Route::get('/oauth_callback/profile', [App\Http\Controllers\OAuthCallbackController::class, 'profile']);

Route::middleware(['auth', 'relogin', 'refresh'])->group(function() {

    Route::resource('projects', App\Http\Controllers\ProjectsController::class);
    Route::get('/project', [App\Http\Controllers\ProjectsController::class, 'showPaginated']);
    Route::get('/projects_export', [App\Http\Controllers\ProjectsController::class, 'export']);

    Route::get('/statistics', [App\Http\Controllers\StatisticsController::class, 'index']);
    Route::get('/statistics/export', [App\Http\Controllers\StatisticsController::class, 'export']);
    Route::get('/statistics/export_detail', [App\Http\Controllers\StatisticsController::class, 'export_detail']);

    Route::middleware(['role:admin,jury'])->group(function() {
        Route::get('/jury', [App\Http\Controllers\JuryController::class, 'index']);
        Route::get('/jury/nominate', [App\Http\Controllers\JuryController::class, 'nominate'])->name('jury.nominate');
    });

    Route::get('/awards', [App\Http\Controllers\AwardsController::class, 'index']);
    Route::get('/awards/{project}/edit', [App\Http\Controllers\AwardsController::class, 'edit'])->name('awards.edit');
    Route::get('/awards/{award}/delete', [App\Http\Controllers\AwardsController::class, 'delete'])->name('awards.delete');
    Route::post('/awards/update', [App\Http\Controllers\AwardsController::class, 'update'])->name('awards.update');

    Route::middleware(['role:jury'])->group(function() {
        Route::post('projects/{project}/set_rating', [App\Http\Controllers\ProjectsController::class, 'setRating'])->name('projects.set_rating');
    });

    Route::middleware(['role:admin'])->group(function() {
        Route::resource('schools', App\Http\Controllers\SchoolsController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::post('schools/{school}/hide', [App\Http\Controllers\SchoolsController::class, 'hide'])->name('schools.hide');
        Route::resource('users', App\Http\Controllers\UsersController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::post('projects/{project}/set_status', [App\Http\Controllers\ProjectsController::class, 'setStatus'])->name('projects.set_status');
        Route::resource('contests', App\Http\Controllers\ContestsController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::post('contests/{contest_id}/activate', [App\Http\Controllers\ContestsController::class, 'activate'])->name('contests.activate');
        Route::get('results', [App\Http\Controllers\ResultsController::class, 'index']);
        Route::get('export/users', [App\Http\Controllers\ExportController::class, 'users']);

        Route::get('admin/users', [App\Http\Controllers\AdminController::class, 'users']);

        Route::get('admin_interface/user/login', [App\Http\Controllers\AdminInterfaceController::class, 'userLogin']);
        Route::get('admin_interface/user/logout', [App\Http\Controllers\AdminInterfaceController::class, 'userLogout']);
        Route::get('admin_interface/user/refresh', [App\Http\Controllers\AdminInterfaceController::class, 'userRefresh']);
        Route::get('admin_interface/user/delete', [App\Http\Controllers\AdminInterfaceController::class, 'showUserDelete']);
        Route::post('admin_interface/user/delete', [App\Http\Controllers\AdminInterfaceController::class, 'userDelete'])->name('admin_interface.user_delete');
    });

    Route::get('user_schools/search', [App\Http\Controllers\UserSchoolsController::class, 'search']);
    Route::post('user_schools/add', [App\Http\Controllers\UserSchoolsController::class, 'add']);
    Route::post('user_schools/create', [App\Http\Controllers\UserSchoolsController::class, 'create']);
    Route::post('user_schools/remove', [App\Http\Controllers\UserSchoolsController::class, 'remove']);
});