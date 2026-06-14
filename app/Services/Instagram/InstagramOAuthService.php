<?php

declare(strict_types=1);

namespace App\Services\Instagram;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class InstagramOAuthService
{
    private const OAUTH_AUTHORIZE_URL = 'https://www.facebook.com/v19.0/dialog/oauth';
    private const OAUTH_TOKEN_URL = 'https://graph.facebook.com/v19.0/oauth/access_token';
    private const GRAPH_BASE_URL = 'https://graph.facebook.com/v19.0';

    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct()
    {
        $this->clientId = config('services.instagram.client_id');
        $this->clientSecret = config('services.instagram.client_secret');
        $this->redirectUri = config('services.instagram.redirect_uri');
    }

    /**
     * Build the Meta OAuth authorization URL with CSRF state.
     */
    public function getAuthorizationUrl(string $state): string
    {
        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'state' => $state,
            'scope' => 'instagram_basic,instagram_content_publish,pages_show_list,pages_read_engagement',
            'response_type' => 'code',
            'display' => 'page',
        ]);

        return self::OAUTH_AUTHORIZE_URL . '?' . $query;
    }

    /**
     * Exchange authorization code for short-lived access token.
     */
    public function exchangeCodeForToken(string $code): array
    {
        $response = Http::get(self::OAUTH_TOKEN_URL, [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $code,
        ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Failed to exchange authorization code');
            throw new RuntimeException($error);
        }

        return $response->json();
    }

    /**
     * Exchange a short-lived token for a long-lived (60-day) token.
     */
    public function getLongLivedToken(string $shortToken): array
    {
        $response = Http::get(self::GRAPH_BASE_URL . '/oauth/access_token', [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'fb_exchange_token' => $shortToken,
        ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Failed to get long-lived token');
            throw new RuntimeException($error);
        }

        return $response->json();
    }

    /**
     * Get Facebook Pages for the authenticated user.
     */
    public function getFacebookPages(string $accessToken): array
    {
        $response = Http::get(self::GRAPH_BASE_URL . '/me/accounts', [
            'access_token' => $accessToken,
        ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Failed to fetch Facebook pages');
            throw new RuntimeException($error);
        }

        return $response->json('data', []);
    }

    /**
     * Get Instagram Business Account ID from a Facebook Page.
     */
    public function getInstagramBusinessAccount(string $pageId, string $accessToken): array
    {
        $response = Http::get(self::GRAPH_BASE_URL . '/' . $pageId, [
            'access_token' => $accessToken,
            'fields' => 'instagram_business_account',
        ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Failed to fetch Instagram business account');
            throw new RuntimeException($error);
        }

        $data = $response->json();

        $igAccount = $data['instagram_business_account'] ?? null;

        if ($igAccount === null) {
            throw new RuntimeException('No Instagram Business Account found for this Facebook Page. Please ensure your Instagram account is connected to a Facebook Page.');
        }

        return $igAccount;
    }

    /**
     * Get Instagram Business Account info (username, etc.).
     */
    public function getInstagramAccountInfo(string $igUserId, string $accessToken): array
    {
        $response = Http::get(self::GRAPH_BASE_URL . '/' . $igUserId, [
            'access_token' => $accessToken,
            'fields' => 'username,profile_picture_url',
        ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Failed to fetch Instagram account info');
            throw new RuntimeException($error);
        }

        return $response->json();
    }

    /**
     * Generate a CSRF state and store in cache.
     */
    public function generateState(): string
    {
        $state = Str::random(40);
        Cache::put('instagram_oauth_state:' . $state, true, now()->addMinutes(10));

        return $state;
    }

    /**
     * Validate the CSRF state parameter from callback.
     */
    public function validateState(string $state): bool
    {
        $key = 'instagram_oauth_state:' . $state;
        $valid = Cache::has($key);

        if ($valid) {
            Cache::forget($key);
        }

        return $valid;
    }
}