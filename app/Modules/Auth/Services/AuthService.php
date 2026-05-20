<?php

declare(strict_types=1);

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\DTOs\RegisterDTO;
use App\Shared\Exceptions\ApiException;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * Authenticate a user and return a JWT token.
     *
     * @throws ApiException
     */
    public function login(LoginDTO $dto): array
    {
        $token = JWTAuth::attempt(['email' => $dto->email, 'password' => $dto->password]);

        if (!$token) {
            throw new ApiException('Invalid email or password.', 401);
        }

        return $this->buildTokenResponse($token);
    }

    /**
     * Register a new user and return a JWT token.
     *
     * @throws ApiException
     */
    public function register(RegisterDTO $dto): array
    {
        if (User::where('email', $dto->email)->exists()) {
            throw new ApiException('An account with this email already exists.', 422);
        }

        $user = User::create([
            'email'    => $dto->email,
            'password' => $dto->password, // Model casts to hashed
            'name'     => $dto->name,
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->buildTokenResponse($token);
    }

    /**
     * Logout — invalidate the current token.
     */
    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    /**
     * Refresh the current JWT token.
     *
     * @throws ApiException
     */
    public function refresh(): array
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            return $this->buildTokenResponse($newToken);
        } catch (\Exception $e) {
            throw new ApiException('Could not refresh token.', 401, [], $e);
        }
    }

    /**
     * Change the authenticated user's password.
     *
     * @throws ApiException
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw new ApiException('Current password is incorrect.', 422);
        }

        $user->update(['password' => $newPassword]);
    }

    /**
     * Send a fake password reset email (stub).
     */
    public function forgotPassword(string $email): void
    {
        // TODO: integrate mail/notification — stub for now
        // If user not found, silently fail to prevent email enumeration
    }

    private function buildTokenResponse(string $token): array
    {
        return [
            'accessToken' => $token,
            'tokenType'   => 'bearer',
            'expiresIn'   => config('jwt.ttl') * 60,
        ];
    }
}
