<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use App\Http\Resources\NoteResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NoteController extends Controller
{
    use AuthorizesRequests;

    // List notes
    public function index(Request $request)
    {
        // Authorise listing of notes
        $this->authorize('viewAny', Note::class);

        // Authenticate user request + paginate
        $notes = Note::where('user_id', $request->user()->id)->get();

        // Return user's notes as collection
        return NoteResource::collection($notes);
    }

    // Store note
    public function store(Request $request)
    {
        // Authorise creation of note
        $this->authorize('create', Note::class);

        // Validate input
        $validated = $request->validate([
            'title'  => 'required|min:3',
            'content' => 'required|min:3'
        ]);

        // Assign authenticated user ID to validated input
        $validated['user_id'] = $request->user()->id;

        // Create instance of note
        $note = Note::create($validated);

        // Return new note as resource
        return (new NoteResource($note))->response()->setStatusCode(201);
    }

    // Show note
    public function show(Request $request, Note $note)
    {
        // Authorise retrieval of note
        $this->authorize('view', $note);

        // return user's note as resource
        return new NoteResource($note);
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

        // Return updated note as resource
        return new NoteResource($note);
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
