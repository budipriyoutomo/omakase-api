<?php

namespace App\Ai\DTOs;

class CampaignPayloadDTO
{
    public function __construct(

        public readonly string $campaignType,

        public readonly string $cuisine,

        public readonly string $platform,

        public readonly string $audience,

        public readonly string $goal,

        public readonly string $mood,

        public readonly string $style,

        public readonly string $heroItem,

        public readonly string $visualStrategy,

        public readonly string $ctaStrategy,

        public readonly string $aspectRatio,

        public readonly string $prompt,

        public readonly ?string $negativePrompt = null,
    ) {}
}
