<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\GoogleLoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(UserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'status' => true,
            'message' => __('messages.success.registered'),
            'user' => new UserResource($user),
        ], 201);
    }
    public function login(AuthLoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = auth()->user();
            $token = $user->generateToken();

            $data = [
                'token' => $token,
                'user' => new UserResource($user),
            ];

            return $this->success($data)->withCookie(cookie('auth_token', $token, 60));
        }

        return $this->error(__('messages.invalid.credentials'));
    }

    public function loginWithGoogle(GoogleLoginRequest $request)
    {
        $params = $request->validated();
        $provider = $params['provider'];
        $validated = $this->validateProvider($provider);
        if (!is_null($validated))
            return $validated;

        $google_user = Socialite::driver($provider)->userFromToken($params['access_provider_token']);
        if ($google_user) {
            $user = User::where('email', $google_user->email)->first();
            if ($user && Auth::loginUsingId($user->id)) {
                $user = auth()->user();
                $token = $user->generateToken();

                $data = [
                    'token' => $token,
                    'user' => new UserResource($user),
                ];

                # NOTE: TOKEN EXPIRES AFTER AN HOUR
                return $this->success($data)->withCookie(cookie('auth_token', $token, 60));
            }
        }

        return $this->error(__('messages.invalid.credentials'));
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.success.deleted'),
        ], 200);
    }

    public function user()
    {
        return response()->json([
            'status' => true,
            'user' => Auth::user(),
        ], 200);
    }

    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['google'])) {
            return $this->error(__('messages.invalid.provider'));
        }
    }
}
