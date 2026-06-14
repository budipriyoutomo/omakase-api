<?php

declare(strict_types=1);

namespace App\Modules\Social\Jobs;

use App\Models\ScheduledPost;
use App\Services\Instagram\InstagramPublishingService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class PublishScheduledPostJob implements ShouldQueue, ShouldBeUnique
{
    use InteractsWithQueue, Queueable;

    public int $tries = 3;

    public int $timeout = 120;

    public array $backoff = [60, 300, 900];

    public function __construct(
        private readonly string $scheduledPostId
    ) {
        $this->onQueue('social-publishing');
    }

    public function uniqueId(): string
    {
        return 'scheduled-post:' . $this->scheduledPostId;
    }

    public function handle(InstagramPublishingService $publishingService): void
    {
        $post = ScheduledPost::with('socialAccount')->findOrFail($this->scheduledPostId);

        // Guard: only process pending posts
        if ($post->status !== ScheduledPost::STATUS_PENDING) {
            Log::info('PublishScheduledPostJob skipped — post not pending', [
                'post_id' => $post->id,
                'current_status' => $post->status,
            ]);
            return;
        }

        // Guard: respect max retries
        if ($post->retry_count >= 3) {
            Log::warning('PublishScheduledPostJob skipped — max retries reached', [
                'post_id' => $post->id,
                'retry_count' => $post->retry_count,
            ]);
            $post->update([
                'status' => ScheduledPost::STATUS_FAILED,
                'error_message' => 'Max retries exceeded (3 attempts)',
            ]);
            return;
        }

        $publishingService->publishPost($post);
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical('PublishScheduledPostJob permanently failed', [
            'post_id' => $this->scheduledPostId,
            'error' => $exception->getMessage(),
        ]);

        $post = ScheduledPost::find($this->scheduledPostId);

        if ($post !== null && $post->status !== ScheduledPost::STATUS_PUBLISHED) {
            $post->update([
                'status' => ScheduledPost::STATUS_FAILED,
                'error_message' => $exception->getMessage(),
                'retry_count' => $post->retry_count + 1,
            ]);
        }
    }
}