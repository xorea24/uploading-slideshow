<?php

namespace App\Http\Controllers;

use App\Models\Slideshow;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SlideshowController; 
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes - Mayor's Office Slideshow System
|--------------------------------------------------------------------------
|
| This file handles the routing for the Admin Dashboard and Public View.
| All administrative routes are protected by the 'auth' middleware.
|
*/

/**
 * SETTINGS MANAGEMENT
 * Update global slideshow configuration (Duration, Transitions)
 */


Route::delete('/slideshow/destroy-album', [SlideshowController::class, 'destroyAlbum'])->name('slideshow.destroy-album');
Route::post('/settings', [SettingsController::class, 'update'])
    ->name('settings.update')
    ->middleware('auth');

/**
 * PUBLIC FACING VIEWS
 * Accessible to everyone; displays the active slideshow
 */
Route::get('/', function () {
    // Fetch only active slides, excluding those in the "Trash" (Soft Deleted)
    $slides = Slideshow::where('is_active', true)
                       ->orderBy('order', 'asc')
                       ->get();

    return view('public', compact('slides'));
});

/**
 * AUTHENTICATION SYSTEM
 * Handles login, logout, and guest redirection
 */
Route::get('/login', function () {
    return view('welcome');
})->name('login')->middleware('guest');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/**
 * SLIDESHOW CONTENT MANAGEMENT (CRUD)
 */
// Upload new images
Route::post('/slideshow', [SlideshowController::class, 'store'])
    ->name('slideshow.store')
    ->middleware('auth');

// Hide/Show a slide without deleting it
Route::patch('/slideshow/{slideshow}/toggle', [SlideshowController::class, 'toggle'])
    ->name('slideshow.toggle')
    ->middleware('auth');

/**
 * TRASH & DELETE MANAGEMENT
 * Utilizing Soft Deletes for safety
 */
// Soft Delete: Move to Trash (sets deleted_at timestamp)
Route::delete('/slideshow/{slideshow}', [SlideshowController::class, 'destroy'])
    ->name('slideshow.destroy')
    ->middleware('auth');

// Restore: Bring back from Trash (clears deleted_at timestamp)
Route::patch('/slideshow/{id}/restore', [SlideshowController::class, 'restore'])
    ->name('slideshow.restore')
    ->middleware('auth');

// Force Delete: Permanent erasure of DB record and physical file
Route::delete('/slideshow/{id}/force', [SlideshowController::class, 'forceDelete'])
    ->name('slideshow.force-delete')
    ->middleware('auth');

/**
 * DASHBOARD & PREVIEW
 */
Route::get('/dashboard', function () {
    // Shows all slides (excluding trashed) in the management grid
    $slides = Slideshow::orderBy('order', 'asc')->get();
    return view('dashboard', compact('slides'));
})->name('dashboard')->middleware('auth');

Route::get('/public-slideshow', function () {
    $slides = Slideshow::where('is_active', true)->get();
    
    // Fetch user-defined settings from the 'settings' table
    $duration = \DB::table('settings')->where('key', 'slide_duration')->value('value') ?? 5;
    $effect = \DB::table('settings')->where('key', 'transition_effect')->value('value') ?? 'fade';

    return view('public-slideshow', compact('slides', 'duration', 'effect'));
});