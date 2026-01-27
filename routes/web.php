<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Route::get('/ressources', [App\Http\Controllers\RessourcesController::class, 'index']);

Route::get('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
Route::get('/profile', [App\Http\Controllers\AuthController::class, 'profile']);
Route::get('/oauth_callback/login', [App\Http\Controllers\OAuthCallbackController::class, 'login']);
Route::get('/oauth_callback/profile', [App\Http\Controllers\OAuthCallbackController::class, 'profile']);

Route::get('/evaluation_server/recreate_mapping', [App\Http\Controllers\EvaluationServerController::class, 'recreateMapping']);
Route::get('/evaluation_server/user_data', [App\Http\Controllers\EvaluationServerController::class, 'getUserData']);
Route::post('/evaluation_server/user_log', [App\Http\Controllers\EvaluationServerController::class, 'receiveQueryUser']);

Route::middleware(['auth', 'relogin', 'refresh'])->group(function() {

    Route::get('/projects/exemple', [App\Http\Controllers\ProjectsController::class, 'exemple']);
    Route::resource('projects', App\Http\Controllers\ProjectsController::class);
    Route::get('/project', [App\Http\Controllers\ProjectsController::class, 'showPaginated']);
    Route::get('/projects/{project}/view', [App\Http\Controllers\ProjectsController::class, 'redirectPaginated']);
    Route::get('/projects/{project}/finalize', [App\Http\Controllers\ProjectsController::class, 'showFinalize'])->name('projects.finalize');
    Route::post('/projects/{project}/confirm-finalize', [App\Http\Controllers\ProjectsController::class, 'confirmFinalize'])->name('projects.confirm_finalize');
    Route::get('/projects_export', [App\Http\Controllers\ProjectsController::class, 'export']);
    Route::get('/projects_zips_generate', [App\Http\Controllers\ProjectsController::class, 'getGenerateZipScript']);
    Route::get('/projects/zips/{zipName}', [App\Http\Controllers\ProjectsController::class, 'downloadZip']);

    Route::get('/statistics', [App\Http\Controllers\StatisticsController::class, 'index']);
    Route::get('/statistics/export', [App\Http\Controllers\StatisticsController::class, 'export']);
    Route::get('/statistics/export_detail', [App\Http\Controllers\StatisticsController::class, 'export_detail']);

    Route::get('/jury', [App\Http\Controllers\JuryController::class, 'index']);
    Route::get('/jury/nominate', [App\Http\Controllers\JuryController::class, 'nominate'])->name('jury.nominate');
    Route::get('/jury/export', [App\Http\Controllers\JuryController::class, 'export'])->name('jury.export');
    Route::get('/jury/exportAll', [App\Http\Controllers\JuryController::class, 'exportAll'])->name('jury.exportAll');

    Route::get('/awards', [App\Http\Controllers\AwardsController::class, 'index']);
    Route::get('/awards/{project}/create', [App\Http\Controllers\AwardsController::class, 'create'])->name('awards.create');
    Route::get('/awards/{award}/edit', [App\Http\Controllers\AwardsController::class, 'edit'])->name('awards.edit');
    Route::get('/awards/{award}/delete', [App\Http\Controllers\AwardsController::class, 'delete'])->name('awards.delete');
    Route::post('/awards/update', [App\Http\Controllers\AwardsController::class, 'update'])->name('awards.update');
    Route::get('/awards/export', [App\Http\Controllers\AwardsController::class, 'export']);

    Route::get('/evaluation_server', [App\Http\Controllers\EvaluationServerController::class, 'index']);

    Route::middleware(['role:jury'])->group(function() {
        Route::post('projects/{project}/set_rating', [App\Http\Controllers\ProjectsController::class, 'setRating'])->name('projects.set_rating');
    });

    Route::middleware(['role:admin'])->group(function() {
        Route::resource('schools', App\Http\Controllers\SchoolsController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::post('schools/{school}/hide', [App\Http\Controllers\SchoolsController::class, 'hide'])->name('schools.hide');
        Route::post('schools/{school}/merge', [App\Http\Controllers\SchoolsController::class, 'merge'])->name('schools.merge');
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

    Route::post('user/update-estimate', [App\Http\Controllers\UsersController::class, 'updateEstimate']);
});