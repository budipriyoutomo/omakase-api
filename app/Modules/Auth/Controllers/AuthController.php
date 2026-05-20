<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controllers;

use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\DTOs\RegisterDTO;
use App\Modules\Auth\Requests\ChangePasswordRequest;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Resources\UserResource;
use App\Modules\Auth\Services\AuthService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly AuthService $authService) {}

    // POST /v1/auth/login
    public function login(LoginRequest $request): JsonResponse
    {
        $tokens = $this->authService->login(LoginDTO::fromArray($request->validated()));

        return $this->success($tokens, 'Logged in successfully.');
    }

    // POST /v1/auth/register
    public function register(RegisterRequest $request): JsonResponse
    {
        $tokens = $this->authService->register(RegisterDTO::fromArray($request->validated()));

        return $this->created($tokens, 'Account created successfully.');
    }

    // POST /v1/auth/logout
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->success(message: 'Logged out successfully.');
    }

    // GET /v1/auth/me
    public function me(): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        return $this->success(new UserResource($user));
    }

    // POST /v1/auth/refresh
    public function refresh(): JsonResponse
    {
        $tokens = $this->authService->refresh();

        return $this->success($tokens, 'Token refreshed.');
    }

    // POST /v1/auth/forgot-password
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);
        $this->authService->forgotPassword($request->email);

        return $this->success(message: 'If that email exists, a reset link has been sent.');
    }

    // POST /v1/auth/change-password
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $data = $request->validated();

        $this->authService->changePassword($user, $data['currentPassword'], $data['newPassword']);

        return $this->success(message: 'Password changed successfully.');
    }

    // POST /v1/auth/me/avatar
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate(['avatar' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:2048']]);

        $user = JWTAuth::parseToken()->authenticate();

        // Remove old avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return $this->success([
            'url' => url('storage/' . $path),
        ]);
    }
}
