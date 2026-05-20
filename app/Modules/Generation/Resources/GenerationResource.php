<?php

declare(strict_types=1);

namespace App\Modules\Generation\Resources;

use App\Models\Generation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Generation
 */
class GenerationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'campaignType' => $this->campaign_type,
            'platform' => $this->platform,
            'style' => $this->style,
            'prompt' => $this->prompt,
            'result' => $this->result,
            'previewUrls' => $this->preview_urls ?? [],
            'metadata' => $this->metadata ?? [],
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}
