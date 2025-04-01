<?php
use Illuminate\Support\Facades\Route;

// Admin routes
Route::prefix('admin')->group(function () {
    Route::get('/{any?}', function () {
        return view('admin');
    })->where('any', '.*');
});

// Frontend routes
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');