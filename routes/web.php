<?php

namespace App\Http\Controllers;

use App\Models\Slideshow;
use App\Models\Album;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SlideshowController; 
use App\Http\Controllers\AlbumController; // Idagdag ito
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes - Mayor's Office Slideshow System
|--------------------------------------------------------------------------
*/
Route::patch('/albums/{album}/toggle', [AlbumController::class, 'toggle'])->name('albums.toggle');
/**
 * DASHBOARD
 */
// DASHBOARD - Ngayon ay dadaan na sa SlideshowController@index para makuha ang $albums
Route::get('/dashboard', [SlideshowController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

/**
 * ALBUM MANAGEMENT
 */
Route::prefix('admin/albums')->middleware('auth')->group(function () {
    // Rename Album
    Route::patch('/{album}/rename', [AlbumController::class, 'update'])->name('albums.update');
    
    // Soft Delete Album (and its contents)
    Route::delete('/{album}', [AlbumController::class, 'destroy'])->name('albums.destroy');
    
    // Recycle Bin Actions for Albums
    Route::patch('/restore-album', [AlbumController::class, 'restoreAlbum'])->name('slideshow.restore-album');
    Route::delete('/{albumId}/force-delete', [AlbumController::class, 'forceDeleteAlbum'])->name('slideshow.delete-album');
});

/**
 * SLIDESHOW / PHOTO MANAGEMENT
 */
Route::prefix('slideshow')->middleware('auth')->group(function () {
    Route::post('/store', [SlideshowController::class, 'store'])->name('slideshow.store');
    Route::patch('/{slideshow}/toggle', [SlideshowController::class, 'toggle'])->name('slideshow.toggle');
    Route::delete('/{slideshow}', [SlideshowController::class, 'destroy'])->name('slideshow.destroy');
    Route::patch('/{id}/restore', [SlideshowController::class, 'restore'])->name('slideshow.restore');
    Route::delete('/{id}/force', [SlideshowController::class, 'forceDelete'])->name('slideshow.force-delete');
});

/**
 * SETTINGS & API
 * 
 * 
 */

// To this:
Route::patch('/settings', [SettingController::class, 'update'])->name('settings.update');
Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update')->middleware('auth');

Route::get('/api/get-latest-settings', function () {
    $lastSetting = DB::table('settings')->max('updated_at');
    $lastImage = DB::table('slideshows')->max('updated_at');
    $lastAlbum = DB::table('albums')->max('updated_at'); // Isama ang album updates
    
    return response()->json([
        'last_update' => max($lastSetting, $lastImage, $lastAlbum)
    ]);
});

/**
 * PUBLIC FACING VIEWS
 */
Route::get('/', function () {
    $displayAlbum = DB::table('settings')->where('key', 'display_album_id')->value('value') ?? 'all';

    if ($displayAlbum === 'all' || $displayAlbum === null) {
        $slides = Slideshow::where('is_active', true)->orderBy('created_at', 'desc')->get();
    } else {
        $slides = Slideshow::where('is_active', true)->where('album_id', $displayAlbum)->orderBy('created_at', 'desc')->get();
    }

    return view('public', compact('slides'));
});

Route::get('/public-slideshow', function () {
    $displayAlbum = DB::table('settings')->where('key', 'display_album_id')->value('value') ?? 'all';
    $duration = DB::table('settings')->where('key', 'slide_duration')->value('value') ?? 5;
    $effect = DB::table('settings')->where('key', 'transition_effect')->value('value') ?? 'fade';

    if ($displayAlbum === 'all' || $displayAlbum === null) {
        $slides = Slideshow::where('is_active', true)->orderBy('created_at', 'desc')->get();
    } else {
        $slides = Slideshow::where('is_active', true)->where('album_id', $displayAlbum)->orderBy('created_at', 'desc')->get();
    }

    return view('public-slideshow', compact('slides', 'duration', 'effect'));
});

/**
 * AUTHENTICATION
 */
Route::get('/login', fn() => view('welcome'))->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');