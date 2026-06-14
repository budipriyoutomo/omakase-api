<?php

declare(strict_types=1);

namespace App\Modules\Social\Resources;

use App\Models\ScheduledPost;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ScheduledPost
 */
class ScheduledPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'campaign_generation_id' => $this->generation_id,
            'image_url' => $this->image_url,
            'caption' => $this->caption,
            'hashtags' => $this->hashtags ?? [],
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'status' => $this->status,
            'published_at' => $this->published_at?->toIso8601String(),
            'instagram_media_id' => $this->instagram_media_id,
            'error_message' => $this->error_message,
            'retry_count' => $this->retry_count,
            'social_account' => new SocialAccountResource($this->whenLoaded('socialAccount')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}