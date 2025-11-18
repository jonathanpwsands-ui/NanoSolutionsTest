<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    // List notes
    public function list() {
        return Note::all();
    }

    // Create note
    public function create(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'title'  => 'required|string|min:3',
            'content' => 'required|string|min:10'
        ]);

        // Create instance of note
        $note = Note::create($validated);

        // Return response confirming note creation
        return response()->json([
            'message' => 'Note created successfully',
            'note' => $note
        ], 201);
    }

    // Retrieve note
    public function retrieve($id) {
        return Note::findOrFail($id);
    }

    // Update note
    public function update(Request $request, $id)
    {
        // Find existing note, otherwise return 404
        $note = Note::findOrFail($id);

        // Validate incoming data
        $validated = $request->validate([
            'title' => 'sometimes|string|min:3',
            'content'  => 'sometimes|string|min:10',
        ]);

        // Update instance of note
        $note->update($validated);

        // Return updated instance of note
        return response()->json($note, 200);
    }

    // Delete note
    public function delete($id)
    {
        // Find existing note, otherwise return 404
        $note = Note::findOrFail($id);

        // Delete model
        $note->delete();

        // Return response confirming note deletion
        return response()->json([
            'message' => 'Post deleted successfully'
        ], 200);
    }
}
