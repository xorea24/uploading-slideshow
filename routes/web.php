<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

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
});

// 1. The Login Page
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::post('/slideshow', [SlideshowController::class, 'store'])->name('slideshow.store')->middleware('auth');

// 3. The Dashboard (Ensure it has the 'auth' middleware)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');