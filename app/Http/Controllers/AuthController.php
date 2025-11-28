<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        // Validate the user details
        $fields = $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|string|email|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8) // Minimum of 8 characters
                    ->letters() // Must include a letter
                    ->mixedCase() // Must include both lower case and upper case
                    ->numbers() // Must include a number
                    ->symbols() // Must include a symbol
                    ->uncompromised() // Checks against known breached passwords from haveibeenpwned.com database
            ],
        ]);

        // Create the user in the database
        $user = User::create([
            'name'     => $fields['name'],
            'email'    => $fields['email'],
            'password' => Hash::make($fields['password']),
        ]);

        // Create an authorisation token that expires after 60 minutes
        $token = $user->createToken('authToken', ['*'], now()->addMinutes(60))->plainTextToken;

        // Return response confirming user and token creation
        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201);
    }
    
    // Log in with an existing user
    public function login(Request $request)
    {
        // Validate login credentials
        $fields = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        // Return response if login credentials do not match
        if (!Auth::attempt($fields)) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        // Authenticate user and create an authentication token
        $user = Auth::user();
        $token = $user->createToken('authToken', ['*'], now()->addMinutes(60))->plainTextToken;

        // Return response confirming user and token authentication
        return response()->json([
            'user'  => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 201);
    }
    
    // Log the current user out by deleting their token
    public function logout(Request $request)
    {
        // Delete the user's token
        $request->user()->currentAccessToken()->delete();

        // Return message confirming logout
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
    
    // Return an authenticated user
    public function me(Request $request){
        return response()->json($request->user());
    }
}
