<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Create user
Route::post('/register', [AuthController::class, 'register']);

// Log in
Route::post('/login',    [AuthController::class, 'login']);

// Requires user authentication to be used
Route::middleware('auth:sanctum')->group(function () {
    // Log out
    Route::post('/logout', [AuthController::class, 'logout']);

    // Retrieve user
    Route::get('/me',      [AuthController::class, 'me']);
    
    // List notes (Index)
    Route::get('/notes', [NoteController::class, 'index']);

    // Store note
    Route::post('/notes', [NoteController::class, 'store']);

    // Show note
    Route::get('/notes/{note}', [NoteController::class, 'show']);

    // Update note
    Route::put('/notes/{note}', [NoteController::class, 'update']);

    // Destroy  note
    Route::delete('/notes/{note}', [NoteController::class, 'destroy']);
});