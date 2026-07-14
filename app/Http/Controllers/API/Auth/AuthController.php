<?php

namespace App\Http\Controllers\API\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ProviderRegisterRequest;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends BaseApiController
{
    //Customer Registration
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'role' => UserRole::CUSTOMER,
            'status' => UserStatus::ACTIVE,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            [
                'user' => $user,
                'token' => $token,
            ],
            'Registration successful.',
            201,
        );
    }

    //Login
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid email or password.', null, 401);
        }

        if ($user->status !== UserStatus::ACTIVE) {
            return $this->errorResponse('Your account is not active.', null, 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            [
                'user' => $user,
                'token' => $token,
            ],
            'Login successful.',
        );
    }

    //profile
    public function profile()
    {
        return $this->successResponse(auth()->user(), 'Profile fetched successfully.');
    }

    //logout
    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully.');
    }

    public function googleRedirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function googleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'phone' => null,
                    'password' => null,
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => UserRole::CUSTOMER,
                    'status' => UserStatus::ACTIVE,
                ]);
            } else {
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse(
                [
                    'user' => $user,
                    'token' => $token,
                ],
                'Google login successful.',
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Google authentication failed.', $e->getMessage(), 500);
        }
    }

    public function providerRegister(ProviderRegisterRequest $request)
    {
        $provider = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'role' => UserRole::PROVIDER,
            'status' => UserStatus::PENDING,
        ]);

        $token = $provider->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            [
                'user' => $provider,
                'token' => $token,
            ],
            'Provider registered successfully. Your account is pending admin approval.',
            201,
        );
    }
}
