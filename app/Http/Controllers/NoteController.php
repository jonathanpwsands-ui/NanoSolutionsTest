<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class NoteController extends Controller
{
    use AuthorizesRequests;

    // List notes
    public function index(Request $request)
    {
        // Authorise listing of notes
        $this->authorize('viewAny', Note::class);

        // Authenticate user request
        $notes = Note::where('user_id', $request->user()->id)->get();

        // Return user's notes
        return response()->json($notes, 200);
    }

    // Store note
    public function store(Request $request)
    {
        // Authorise creation of note
        $this->authorize('create', Note::class);

        // Validate input
        $validated = $request->validate([
            'title'  => 'required|min:3|max:255',
            'content' => 'required|min:3|max:10000'
        ]);

        // Sanitize input
        $validated['title'] = Str::limit(strip_tags($validated['title']), 255);
        $validated['content'] = strip_tags($validated['content'], '<p><br><strong><em><ul><ol><li>');

        // Assign authenticated user ID to validated input
        $validated['user_id'] = $request->user()->id;

        // Create instance of note
        $note = Note::create($validated);

        // Return response confirming note creation
        return response()->json($note, 201);
    }

    // Show note
    public function show(Request $request, Note $note)
    {
        // Authorise retrieval of note
        $this->authorize('view', $note);

        // return user's note
        return response()->json($note, 200);
    }

    // Update note
    public function update(Request $request, Note $note)
    {
        // Authorise updating of note
        $this->authorize('update', $note);

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

    // Destroy note
    public function destroy(Request $request, Note $note)
    {
        // Authorise deltion of note
        $this->authorize('delete', $note);

        // Delete instance of note
        $note->delete();

        // Return response confirming note deletion
        return response()->json(['message' => 'Note deleted successfully'], 200);
    }
}
