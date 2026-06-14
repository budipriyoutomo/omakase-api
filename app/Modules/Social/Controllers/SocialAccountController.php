<?php

declare(strict_types=1);

namespace App\Modules\Social\Controllers;

use App\Models\SocialAccount;
use App\Modules\Social\Resources\SocialAccountResource;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class SocialAccountController extends Controller
{
    use ApiResponseTrait;

    /**
     * GET /v1/social-accounts
     * List connected social accounts for the authenticated user.
     */
    public function index(): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        $accounts = SocialAccount::where('user_id', $user->id)
            ->latest('connected_at')
            ->get();

        return $this->success(SocialAccountResource::collection($accounts));
    }

    /**
     * DELETE /v1/social-accounts/{id}
     * Disconnect a social account.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        $account = SocialAccount::where('user_id', $user->id)->findOrFail($id);

        $account->delete();

        return $this->success(null, 'Social account disconnected successfully.');
    }
}