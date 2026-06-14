<?php

declare(strict_types=1);

namespace App\Modules\Social\Controllers;

use App\Models\SocialAccount;
use App\Services\Instagram\InstagramOAuthService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class InstagramOAuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly InstagramOAuthService $oauthService
    ) {}

    /**
     * GET /v1/instagram/auth-url
     * Return the Meta OAuth authorization URL.
     */
    public function authUrl(): JsonResponse
    {
        $state = $this->oauthService->generateState();
        $url = $this->oauthService->getAuthorizationUrl($state);

        return $this->success([
            'url' => $url,
            'state' => $state,
        ]);
    }

    /**
     * GET /v1/instagram/callback?code=&state=
     * Handle the OAuth callback from Meta.
     */
    public function callback(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        $code = $request->query('code');
        $state = $request->query('state');

        if (empty($code)) {
            return $this->error('Authorization code is required.', 400);
        }

        if (empty($state) || !$this->oauthService->validateState($state)) {
            return $this->error('Invalid or expired state parameter.', 400);
        }

        try {
            // Exchange code for short-lived token
            $tokenData = $this->oauthService->exchangeCodeForToken($code);
            $shortToken = $tokenData['access_token'] ?? null;

            if ($shortToken === null) {
                return $this->error('Failed to obtain access token from Meta.', 400);
            }

            // Exchange for long-lived token (60 days)
            $longTokenData = $this->oauthService->getLongLivedToken($shortToken);
            $accessToken = $longTokenData['access_token'] ?? $shortToken;
            $expiresIn = $longTokenData['expires_in'] ?? 5184000; // default 60 days

            $tokenExpiresAt = now()->addSeconds((int) $expiresIn);

            // Get Facebook Pages
            $pages = $this->oauthService->getFacebookPages($accessToken);

            if (empty($pages)) {
                return $this->error('No Facebook Pages found. Please ensure you have a Facebook Page connected to your Instagram Business Account.', 400);
            }

            // Use the first page
            $page = $pages[0];
            $pageId = $page['id'];
            $pageAccessToken = $page['access_token'] ?? $accessToken;

            // Get Instagram Business Account
            $igBusinessAccount = $this->oauthService->getInstagramBusinessAccount($pageId, $pageAccessToken);
            $igUserId = $igBusinessAccount['id'];

            // Get Instagram account info (username)
            $igInfo = $this->oauthService->getInstagramAccountInfo($igUserId, $accessToken);
            $igUsername = $igInfo['username'] ?? null;

            // Create or update SocialAccount record
            $socialAccount = SocialAccount::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'platform' => 'instagram',
                    'platform_user_id' => $igUserId,
                ],
                [
                    'platform_username' => $igUsername,
                    'access_token' => $accessToken,
                    'token_expires_at' => $tokenExpiresAt,
                    'facebook_page_id' => $pageId,
                    'is_active' => true,
                    'connected_at' => now(),
                ]
            );

            Log::info('Instagram account connected', [
                'user_id' => $user->id,
                'account_id' => $socialAccount->id,
                'ig_username' => $igUsername,
            ]);

            return $this->success($socialAccount->getInstagramAccountInfo(), 'Instagram account connected successfully.');

        } catch (\Throwable $e) {
            Log::error('Instagram OAuth callback failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return $this->error('Failed to connect Instagram account: ' . $e->getMessage(), 500);
        }
    }
}