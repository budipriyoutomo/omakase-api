<?php

declare(strict_types=1);

namespace App\Ai\DTOs;

use App\Models\Generation;

final class CampaignPayloadDTO
{
    public function __construct(
        public readonly string $goal,
        public readonly string $platform,
        public readonly string $audience,
        public readonly string $style,
        public readonly string $mood,
        public readonly string $heroItem,
        public readonly string $cuisine,
        public readonly string $campaignType,
        public readonly string $visualStrategy,
        public readonly string $ctaStrategy,
        public readonly string $aspectRatio,
        public readonly ?string $prompt = null,
        public readonly ?string $negativePrompt = null,

    ) {}

    public static function fromGeneration(
        Generation $generation
    ): self {
        return new self(
            goal:           $generation->goal           ?? '',
            platform:       $generation->platform       ?? '',
            audience:       $generation->audience       ?? '',
            style:          $generation->style          ?? '',
            mood:           $generation->mood           ?? '',
            heroItem:       $generation->hero_item      ?? '',
            cuisine:        $generation->cuisine        ?? '',
            campaignType:   $generation->campaign_type  ?? '',
            visualStrategy: $generation->visual_strategy ?? '',
            ctaStrategy:    $generation->cta_strategy   ?? '',
            aspectRatio:    $generation->aspect_ratio   ?? '',

        );
    }

    public function toArray(): array
    {
        return [
            'goal'            => $this->goal,
            'platform'        => $this->platform,
            'audience'        => $this->audience,
            'style'           => $this->style,
            'mood'            => $this->mood,
            'hero_item'       => $this->heroItem,
            'cuisine'         => $this->cuisine,
            'campaign_type'   => $this->campaignType,
            'visual_strategy' => $this->visualStrategy,
            'cta_strategy'    => $this->ctaStrategy,
            'aspect_ratio'    => $this->aspectRatio,
        ];
    }
}