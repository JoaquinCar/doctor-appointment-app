<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function(){
    return view('admin.dashboard');
})->name('dashboard');

//gestion de roles
Route::resource('roles',\App\Http\Controllers\Admin\RoleController::class);

//gestion de usuarios
Route::resource('users',\App\Http\Controllers\Admin\UserController::class);
