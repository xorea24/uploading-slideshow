<?php

namespace App\Http\Controllers;

use App\Http\Controllers\PhotoController;
use App\Models\Photo;
use App\Models\Album;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AlbumController; // Idagdag ito
use App\Http\Controllers\AuthController;


/*
|--------------------------------------------------------------------------
| Web Routes - Mayor's Office Photo System
|--------------------------------------------------------------------------
*/
// This is for the Slideshow frontend to check for changes
// The name 'Photo.update' must match your route() helper in Blade

// web.php
// Check if the name 'Photo.toggle' matches your Blade file
// Add this route for the toggle functionality


// Ensure this matches the URL in your fetch() 
Route::middleware(['auth'])->group(function () {
    // DASHBOARD
    Route::get('/dashboard', [PhotoController::class, 'index'])->name('dashboard');
    // PHOTO MANAGEMENT
    Route::post('/upload', [PhotoController::class, 'store'])->name('photos.store');
    // Siguraduhin na 'photos.update' ang name at PATCH ang method
    // Siguraduhin na PATCH ito at tumutugma ang 'photo.update'
    Route::patch('{photo}', [PhotoController::class, 'update'])->name('photos.update');
    Route::get('/photos/{photo}/toggle', [PhotoController::class, 'toggle'])->name('photos.toggle');
    Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');
        // 4. DESTROY / DELETE
    });

/**
 * ALBUM MANAGEMENT
 */
Route::prefix('admin/albums')->middleware('auth')->group(function () {
    // Rename Album
    Route::patch('/{album}/rename', [AlbumController::class, 'update'])->name('albums.update');
    
    // Soft Delete Album (and its contents)
    Route::delete('/{album}', [AlbumController::class, 'destroy'])->name('albums.destroy');
    
    // Recycle Bin Actions for Albums
    Route::patch('/restore-album', [AlbumController::class, 'restoreAlbum'])->name('photos.restore-album');
    Route::delete('/{albumId}/force-delete', [AlbumController::class, 'forceDeleteAlbum'])->name('Photo.delete-album');
    Route::patch('/albums/{album}/toggle', [AlbumController::class, 'toggle'])->name('albums.toggle');
});

/**
 * Photo / Photo MANAGEMENT
 */
Route::prefix('Photo')->middleware('auth')->group(function () {
    Route::post('/store', [PhotoController::class, 'store'])->name('Photo.store');
    Route::patch('/{Photo}/toggle', [PhotoController::class, 'toggle'])->name('Photo.toggle');
    Route::delete('{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');
    Route::patch('/{id}/restore', [PhotoController::class, 'restore'])->name('photos.restore');
    Route::delete('/photos/{id}/force', [PhotoController::class, 'forceDelete'])->name('photos.forceDelete');
});

/**
 * SETTINGS & API
 *
 *
 */
Route::get('/settings/latest', [SettingsController::class, 'getLatestData']);

Route::post('/settings/update', [SettingsController::class, 'update'])->name('settings.update');
// Add this line to handle the photo updates (title and description)
// Siguraduhin na ang URL ay /photos/{id}/update
Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update')->middleware('auth');

// Siguraduhin na ang URL na ito ang tinatawag sa iyong JS fetch
Route::get('/api/get-latest-settings', function () {
    $lastSetting = DB::table('settings')->max('updated_at');
    $lastImage = DB::table('photos')->max('updated_at');
    $lastAlbum = DB::table('albums')->max('updated_at'); // Isama ang album updates

    return response()->json([
        'last_update' => max($lastSetting, $lastImage, $lastAlbum)
    ]);
});


/**
 * PUBLIC FACING VIEWS
 */
Route::get('/', function () {
    $displayAlbums = DB::table('settings')->where('key', 'display_album_ids')->value('value') ?? '';

    if ($displayAlbums === '' || $displayAlbums === null) {
        $slides = Photo::where('is_active', true)->orderBy('created_at', 'desc')->get();
    } else {
        $albumIds = array_map('intval', explode(',', $displayAlbums));
        $slides = Photo::where('is_active', true)->whereIn('album_id', $albumIds)->orderBy('created_at', 'desc')->get();
    }

    return view('public', compact('slides'));
});

Route::get('/public-Photo', function () {
    $displayAlbum = DB::table('settings')->where('key', 'display_album_id')->value('value') ?? 'all';
    $duration = DB::table('settings')->where('key', 'slide_duration')->value('value') ?? 5;
    $effect = DB::table('settings')->where('key', 'transition_effect')->value('value') ?? 'fade';

    if ($displayAlbum === 'all' || $displayAlbum === null) {
        $slides = Photo::where('is_active', true)->orderBy('created_at', 'desc')->get();
    } else {
        $slides = Photo::where('is_active', true)->where('album_id', $displayAlbum)->orderBy('created_at', 'desc')->get();
    }

    return view('public-Photo', compact('slides', 'duration', 'effect'));
});

/**
 * AUTHENTICATION
 */
Route::get('/login', fn() => view('welcome'))->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');