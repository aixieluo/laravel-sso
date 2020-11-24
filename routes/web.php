<?php

use Illuminate\Support\Facades\Route;
use Aixieluo\LaravelSso\Http\Controllers\UserController;

Route::get('code', [UserController::class, 'code'])->name('oauth.code');
Route::any('get/token', [UserController::class, 'accessToken']);
Route::post('logout', [UserController::class, 'logout']);
