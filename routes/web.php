<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root to users index
Route::get('/', [UserController::class, 'index'])->name('home');

// RESTful resource routes for User CRUD
Route::resource('users', UserController::class);

// Interactive Eloquent code runner
Route::post('/run-snippet', [UserController::class, 'runSnippet'])->name('users.run');

// Eloquent vs Vanilla PDO comparison guide
Route::get('/guide', [UserController::class, 'guide'])->name('guide');
