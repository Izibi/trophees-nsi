<?php

use Illuminate\Support\Facades\Route;


Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('presentation', [App\Http\Controllers\PresentationController::class, 'index']);
Route::get('reglament', [App\Http\Controllers\ReglamentController::class, 'index']);
Route::get('results', [App\Http\Controllers\ResultsController::class, 'index']);

Route::resource('schools', App\Http\Controllers\SchoolsController::class);
Route::resource('users', App\Http\Controllers\UsersController::class);
Route::resource('projects', App\Http\Controllers\ProjectsController::class);