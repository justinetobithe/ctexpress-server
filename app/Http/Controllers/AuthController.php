<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:15|unique:users',
            'address' => 'required|string|max:255',
            'role' => 'required|in:Passenger,Driver,Admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    /**
     * Login user.
     */
    public function login(Request $request)
    {
        if ($request->has('google_token')) {
            try {
                $googleUser = Socialite::driver('google')->userFromToken($request->google_token);

                $user = User::where('email', $googleUser->getEmail())->first();

                if (!$user) {
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'google_avatar' => $googleUser->getAvatar(),
                        'role' => 'Passenger',
                    ]);
                }

                Auth::login($user, true);

                return response()->json([
                    'status' => true,
                    'message' => 'Logged in successfully with Google',
                    'user' => $user,
                    'token' => $user->createToken('API Token')->plainTextToken,
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to login with Google',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                return response()->json([
                    'status' => true,
                    'message' => 'Logged in successfully',
                    'user' => $user,
                    'token' => $user->createToken('API Token')->plainTextToken,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid email or password',
                ]);
            }
        }
    }


    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }

    /**
     * Get the authenticated user.
     */
    public function user()
    {
        return response()->json([
            'status' => true,
            'user' => Auth::user(),
        ], 200);
    }
}
