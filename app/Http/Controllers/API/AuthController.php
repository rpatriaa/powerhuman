<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // fungsi login 
    public function login(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            // Find the user by email
            $credentials = $request->only(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error(null, 'Unauthorized', 401);
            }

            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password)) {
                throw new \Exception('Invalid password');
            }


            // Generate a token for the user
            $token = $user->createToken('authToken')->plainTextToken;

            // Return response with the token
            return ResponseFormatter::success([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Login successful');
        } catch (\Exception $error) {
            return ResponseFormatter::error('Authentication Failed');
        }
    }

    public function register(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);

            // contoh validasi cara yang lain
            // $request->validate([
            //     'name' => 'required|string|max:255',
            //     'email' => 'required|string|email|max:255|unique:users',
            //     'password' => 'required|string|min:6|confirmed',
            // ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(
                    $validator->errors(),
                    'Validation Error',
                    422
                );
            }

            // Create a new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Generate a token for the user
            $token = $user->createToken('authToken')->plainTextToken;

            // Return response with the token
            return ResponseFormatter::success([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Registration successful');
        } catch (\Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 'Registration Failed', 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Revoke the token
            $request->user()->currentAccessToken()->delete();

            // Return success response
            return ResponseFormatter::success(null, 'Logout successful');
        } catch (\Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 'Logout Failed', 500);
        }
    }
}
