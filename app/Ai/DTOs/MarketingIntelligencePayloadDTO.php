<?php

declare(strict_types=1);

namespace App\Ai\DTOs;

use App\Models\Generation;

/**
 * DTO khusus untuk marketing intelligence — berisi field marketing/campaign.
 * Diproses terpisah dari generate gambar.
 */
final class MarketingIntelligencePayloadDTO
{
    public function __construct(
        public readonly string $campaignType,
        public readonly string $platform,
        public readonly string $audience,
        public readonly string $goal,
        public readonly string $visualStrategy,
        public readonly string $ctaStrategy,
    ) {}

    public static function fromGeneration(Generation $generation): self
    {
        return new self(
            campaignType:   $generation->campaign_type   ?? '',
            platform:       $generation->platform        ?? '',
            audience:       $generation->audience        ?? '',
            goal:           $generation->goal            ?? '',
            visualStrategy: $generation->visual_strategy ?? '',
            ctaStrategy:    $generation->cta_strategy    ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'campaign_type'   => $this->campaignType,
            'platform'        => $this->platform,
            'audience'        => $this->audience,
            'goal'            => $this->goal,
            'visual_strategy' => $this->visualStrategy,
            'cta_strategy'    => $this->ctaStrategy,
        ];
    }
}