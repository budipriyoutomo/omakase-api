<?php

declare(strict_types=1);

namespace App\Modules\User\Controllers;

use App\Modules\Auth\Resources\UserResource;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    use ApiResponseTrait;

    // GET /v1/users/me
    public function show(): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        return $this->success(new UserResource($user));
    }

    // PATCH /v1/users/me
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'               => ['nullable', 'string', 'max:255'],
            'emailNotifications' => ['nullable', 'boolean'],
            'marketingEmails'    => ['nullable', 'boolean'],
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        $mapped = [];
        if (array_key_exists('name', $data))               $mapped['name']                = $data['name'];
        if (array_key_exists('emailNotifications', $data))  $mapped['email_notifications'] = $data['emailNotifications'];
        if (array_key_exists('marketingEmails', $data))     $mapped['marketing_emails']    = $data['marketingEmails'];

        if (!empty($mapped)) {
            $user->update($mapped);
        }

        return $this->success(new UserResource($user->fresh()));
    }
}
