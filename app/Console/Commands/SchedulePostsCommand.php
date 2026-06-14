<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ScheduledPost;
use App\Modules\Social\Jobs\PublishScheduledPostJob;
use Illuminate\Console\Command;

class SchedulePostsCommand extends Command
{
    protected $signature = 'posts:publish-due';

    protected $description = 'Dispatch publish jobs for all past-due scheduled posts';

    public function handle(): int
    {
        $posts = ScheduledPost::pending()
            ->where('scheduled_at', '<=', now())
            ->where('retry_count', '<', 3)
            ->get();

        if ($posts->isEmpty()) {
            $this->info('No past-due scheduled posts to publish.');

            return self::SUCCESS;
        }

        foreach ($posts as $post) {
            PublishScheduledPostJob::dispatch($post->id);
            $this->info("Dispatched PublishScheduledPostJob for post {$post->id}");
        }

        $this->info("Dispatched {$posts->count()} posts for publishing.");

        return self::SUCCESS;
    }
}