<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    // List notes
    public function list() {
        return response()->json(Note::all(), 200);
    }

    // Create note
    public function create(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'title'  => 'required|min:3',
            'content' => 'required|min:3'
        ]);

        // Create instance of note
        $note = Note::create($validated);

        // Return response confirming note creation
        return response()->json($note, 201);
    }

    // Retrieve note
    public function retrieve(Note $note) {
        return response()->json($note, 200);
    }

    // Update note
    public function update(Request $request, Note $note)
    {
        // Validate incoming data
        $validated = $request->validate([
            'title'   => 'required|min:3',
            'content' => 'required|min:3',
        ]);

        // Update instance of note
        $note->update($validated);

        // Return updated instance of note
        return response()->json($note, 200);
    }

    // Delete note
    public function delete(Note $note)
    {
        // Delete model
        $note->delete();

        // Return response confirming note deletion
        return response()->json(['message' => 'Note deleted successfully'], 200);
    }
}
