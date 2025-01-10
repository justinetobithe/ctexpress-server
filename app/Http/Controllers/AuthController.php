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
        $validated = $request->validated();

        if (User::where('email', $validated['email'])->exists()) {
            return response()->json([
                'status' => false,
                'message' => __('messages.errors.email_exists'),
            ]);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = md5(uniqid() . date('u')) . '.' . pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $upload = $image->storeAs("/public/image", $imageName);
            if ($upload) {
                $validated['image'] = $imageName;
            }
        }

        $validated['password'] = Hash::make($validated['password']);

        $classification = $validated['classification'] ?? '';
        $validated['status'] = ($classification === 'none' || $classification === '') ? 1 : 0;

        $user = User::create($validated);

        return response()->json([
            'status' => true,
            'message' => __('messages.success.registered'),
            'user' => new UserResource($user),
        ]);
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

            return response()->json([
                'status' => true,
                'message' => __('messages.success.login'),
                'data' => $data,
            ])->withCookie(cookie('auth_token', $token, 60));
        }

        return response()->json([
            'status' => false,
            'message' => __('messages.invalid.credentials'),
            'data' => null
        ]);
    }

    public function mobileLogin(AuthLoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = auth()->user();
            if ($user->status === 0) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.errors.needs_approval'),
                ]);
            }

            if ($user->role !== 'passenger' && $user->role !== 'driver') {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.errors.role_access_denied'),
                ]);
            }

            $token = $user->generateToken();

            $data = [
                'token' => $token,
                'user' => new UserResource($user),
            ];

            return response()->json([
                'status' => true,
                'message' => __('messages.success.login'),
                'data' => $data,
            ])->withCookie(cookie('auth_token', $token, 60));
        }

        return response()->json([
            'status' => false,
            'message' => __('messages.invalid.credentials'),
            'data' => null
        ]);
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
        ]);
    }

    public function user()
    {
        return response()->json([
            'status' => true,
            'user' => Auth::user(),
        ]);
    }

    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['google'])) {
            return $this->error(__('messages.invalid.provider'));
        }
    }
}
