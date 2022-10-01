<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name("home");
Route::any('/registerform', function () {
    return view('register');
})->name("registerform");

Route::any("login",[AuthController::class,"login"])->name("login");
Route::any("register",[AuthController::class,"register"])->name("register");
Route::any("logout",[AuthController::class,"logout"])->name("logout");
Route::post("upload",[FileController::class,"store"])->name("upload");
Route::any("allfiles",[FileController::class,"index"])->name("allfiles");



