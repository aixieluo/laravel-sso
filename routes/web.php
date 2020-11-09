<?php

use Illuminate\Support\Facades\Route;
use Aixieluo\LaravelSso\Http\Controllers\UserController;

Route::get('oauth/code', [UserController::class, 'code']);
Route::any('oauth/get/token', [UserController::class, 'accessToken']);
