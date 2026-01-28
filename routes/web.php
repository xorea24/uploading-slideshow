<?php
namespace App\Http\Controllers;
use App\Models\Slideshow;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SlideshowController; // Make sure this is at the top



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


// Fix the settings route
Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update')->middleware('auth');

Route::get('/', function () {
    // Kunin lang ang mga active at ayon sa pagkakasunod-sunod
    $slides = Slideshow::where('is_active', true)
                       ->orderBy('order', 'asc')
                       ->get();

    return view('public', compact('slides'));
});

// 1. The Login Page
Route::get('/login', function () {
    return view('welcome');
})->name('login')->middleware('guest');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::post('/slideshow', [SlideshowController::class, 'store'])->name('slideshow.store')->middleware('auth');

Route::patch('/slideshow/{slideshow}/toggle', [SlideshowController::class, 'toggle'])->name('slideshow.toggle')->middleware('auth');

Route::delete('/slideshow/{slideshow}', [SlideshowController::class, 'destroy'])->name('slideshow.destroy')->middleware('auth');

// 3. The Dashboard (Ensure it has the 'auth' middleware)
Route::get('/dashboard', function () {
    $slides = Slideshow::orderBy('order', 'asc')->get();
    return view('dashboard', compact('slides'));
})->name('dashboard')->middleware('auth');

Route::get('/public-slideshow', function () {
    $slides = \App\Models\Slideshow::where('is_active', true)->get();
    
    // Fetch the settings or use defaults
    $duration = \DB::table('settings')->where('key', 'slide_duration')->value('value') ?? 5;
    $effect = \DB::table('settings')->where('key', 'transition_effect')->value('value') ?? 'fade';

    return view('public-slideshow', compact('slides', 'duration', 'effect'));
});

