<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// List notes
Route::get('/notes', [NoteController::class, 'list']);

// Create note
Route::post('/notes', [NoteController::class, 'create']);

// Retrieve note
Route::get('/notes/{id}', [NoteController::class, 'retrieve']);

// Update note
Route::put('/notes/{id}', [NoteController::class, 'update']);

// Delete note
Route::delete('/notes/{id}', [NoteController::class, 'delete']);