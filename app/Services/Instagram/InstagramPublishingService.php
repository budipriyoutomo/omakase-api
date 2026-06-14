<?php

declare(strict_types=1);

namespace App\Services\Instagram;

use App\Exceptions\InstagramPublishException;
use App\Models\ScheduledPost;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstagramPublishingService
{
    private const GRAPH_BASE_URL = 'https://graph.facebook.com/v19.0';
    private const MAX_STATUS_CHECKS = 10;
    private const STATUS_CHECK_INTERVAL = 3; // seconds

    /**
     * Create a media container on Instagram.
     *
     * @return string The container ID.
     */
    public function createMediaContainer(SocialAccount $account, string $imageUrl, string $caption): string
    {
        $response = Http::post(self::GRAPH_BASE_URL . '/' . $account->platform_user_id . '/media', [
            'image_url' => $imageUrl,
            'caption' => $caption,
            'access_token' => $account->access_token,
        ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Failed to create media container');
            Log::error('Instagram media container creation failed', [
                'account_id' => $account->id,
                'error' => $error,
                'response' => $response->json(),
            ]);
            throw new InstagramPublishException($error);
        }

        return $response->json('id');
    }

    /**
     * Publish a media container to Instagram.
     *
     * @return string The Instagram media ID.
     */
    public function publishMediaContainer(SocialAccount $account, string $containerId): string
    {
        $response = Http::post(self::GRAPH_BASE_URL . '/' . $account->platform_user_id . '/media_publish', [
            'creation_id' => $containerId,
            'access_token' => $account->access_token,
        ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Failed to publish media container');
            Log::error('Instagram media publish failed', [
                'account_id' => $account->id,
                'container_id' => $containerId,
                'error' => $error,
                'response' => $response->json(),
            ]);
            throw new InstagramPublishException($error);
        }

        return $response->json('id');
    }

    /**
     * Check the status of a media container.
     *
     * @return string Status code: EXPIRED, ERROR, FINISHED, IN_PROGRESS, PUBLISHED
     */
    public function getContainerStatus(SocialAccount $account, string $containerId): string
    {
        $response = Http::get(self::GRAPH_BASE_URL . '/' . $containerId, [
            'access_token' => $account->access_token,
            'fields' => 'status_code',
        ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Failed to check container status');
            Log::error('Instagram container status check failed', [
                'account_id' => $account->id,
                'container_id' => $containerId,
                'error' => $error,
            ]);
            throw new InstagramPublishException($error);
        }

        return $response->json('status_code', 'ERROR');
    }

    /**
     * Orchestrate the full publishing workflow:
     * create container → poll status → publish.
     */
    public function publishPost(ScheduledPost $post): bool
    {
        $account = $post->socialAccount;

        if ($account === null) {
            throw new InstagramPublishException('Social account not found');
        }

        if ($account->isTokenExpired()) {
            throw new InstagramPublishException('Instagram access token has expired. Please reconnect your account.');
        }

        // Update status to processing
        $post->update([
            'status' => ScheduledPost::STATUS_PROCESSING,
        ]);

        try {
            // Build caption with hashtags
            $caption = $post->caption;
            $hashtags = $post->hashtags ?? [];
            if (!empty($hashtags)) {
                $caption .= "\n\n" . implode(' ', array_map(
                    fn(string $tag): string => str_starts_with($tag, '#') ? $tag : '#' . $tag,
                    $hashtags
                ));
            }

            // Step 1: Create media container
            $containerId = $this->createMediaContainer($account, $post->image_url, $caption);

            // Step 2: Poll container status until finished
            $status = $this->getContainerStatus($account, $containerId);
            $attempts = 1;

            while ($status === 'IN_PROGRESS' && $attempts < self::MAX_STATUS_CHECKS) {
                sleep(self::STATUS_CHECK_INTERVAL);
                $status = $this->getContainerStatus($account, $containerId);
                $attempts++;
            }

            if ($status === 'EXPIRED') {
                throw new InstagramPublishException('Media container expired before publishing');
            }

            if ($status === 'ERROR') {
                throw new InstagramPublishException('Media container failed with an error');
            }

            if ($status !== 'FINISHED' && $status !== 'PUBLISHED') {
                throw new InstagramPublishException("Unexpected container status after {$attempts} checks: {$status}");
            }

            // Step 3: Publish the container
            $mediaId = $this->publishMediaContainer($account, $containerId);

            // Mark as published
            $post->update([
                'status' => ScheduledPost::STATUS_PUBLISHED,
                'instagram_media_id' => $mediaId,
                'published_at' => now(),
            ]);

            Log::info('Instagram post published successfully', [
                'post_id' => $post->id,
                'instagram_media_id' => $mediaId,
            ]);

            return true;

        } catch (\Throwable $e) {
            $errorMessage = $e instanceof InstagramPublishException
                ? $e->getMessage()
                : 'Unexpected publishing error: ' . $e->getMessage();

            $post->update([
                'status' => ScheduledPost::STATUS_FAILED,
                'error_message' => $errorMessage,
                'retry_count' => $post->retry_count + 1,
            ]);

            Log::error('Instagram publishing failed', [
                'post_id' => $post->id,
                'error' => $errorMessage,
            ]);

            if (!($e instanceof InstagramPublishException)) {
                throw $e;
            }

            return false;
        }
    }
}