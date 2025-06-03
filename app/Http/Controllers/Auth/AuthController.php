<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        // Check if user already exists
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json([
                'message' => 'User with this email already exists',
                'status' => 'error'
            ], 409);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => bcrypt($request->password),
            ]);



            return response()->json([
                'data' => [
                    'user' => $user,
                ],
                'message' => 'Registration successful',
                'status' => 'success'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Registration failed: ' . $th->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        $credentials = $request->only('email', 'password');
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized',
                'status' => 'error'
            ], 401);
        }
        $user = auth()->user();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $token
            ],
            'message' => 'Login successful',
            'status' => 'success'
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $user->tokens()->delete();
            return response()->json([
                'message' => 'Logout successful',
                'status' => 'success'
            ], 200);
        }
        return response()->json([
            'message' => 'User not authenticated',
            'status' => 'error'
        ], 401);
    }
}
