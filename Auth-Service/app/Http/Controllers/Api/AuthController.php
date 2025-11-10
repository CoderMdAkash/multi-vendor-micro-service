<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request) 
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:6|max:50',
            'password_confirmation' => 'required|same:password'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        unset($validated['password_confirmation']);

        try {
            $user = User::create($validated);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }

        return response()->json([  
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not create token'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'User logged in successfully',
            'data' => [
                'token' => $token,
                'expires_in' => JWTAuth::factory()->getTTL() * 60 * 24
            ]
        ]);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully',
            ], 200);

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout, please try again.',
            ], 500);
        }
    }

    public function me(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User info retrieved successfully',
                'data' => [
                    'user' => $user,
                ],
            ], 200);

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token is invalid or expired',
            ], 401);
        }
    }

}
