<?php

declare(strict_types=1);

namespace App\Modules\Social\Resources;

use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SocialAccount
 */
class SocialAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'platform' => $this->platform,
            'platform_username' => $this->platform_username,
            'is_active' => $this->is_active,
            'connected_at' => $this->connected_at?->toIso8601String(),
            'token_expires_at' => $this->token_expires_at?->toIso8601String(),
        ];
    }
}