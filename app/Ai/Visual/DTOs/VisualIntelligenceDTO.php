<?php

declare(strict_types=1);

namespace App\Ai\Visual\DTOs;

use App\Ai\Contracts\ArrayableData;

final class VisualIntelligenceDTO implements ArrayableData
{
    public function __construct(
        public readonly string $heroFocus,
        public readonly string $compositionType,
        public readonly string $campaignVisualFormat,
        public readonly string $realismLevel,
        public readonly string $photographyStyle,
        public readonly string $cameraAngle,
        public readonly string $lighting,
        public readonly string $negativeSpace,
        public readonly string $typographySafeLayout,
        public readonly string $ctaSafeSpacing,
        public readonly string $foodTexture,
        public readonly string $layoutStrategy,
        public readonly string $visualHierarchy,
        public readonly string $director,
        public readonly array $commercialDirectives = [],
        public readonly array $metadata = [],
    ) {}

    public function withOverrides(array $overrides): self
    {
        return new self(
            heroFocus: $overrides['heroFocus'] ?? $this->heroFocus,
            compositionType: $overrides['compositionType'] ?? $this->compositionType,
            campaignVisualFormat: $overrides['campaignVisualFormat'] ?? $this->campaignVisualFormat,
            realismLevel: $overrides['realismLevel'] ?? $this->realismLevel,
            photographyStyle: $overrides['photographyStyle'] ?? $this->photographyStyle,
            cameraAngle: $overrides['cameraAngle'] ?? $this->cameraAngle,
            lighting: $overrides['lighting'] ?? $this->lighting,
            negativeSpace: $overrides['negativeSpace'] ?? $this->negativeSpace,
            typographySafeLayout: $overrides['typographySafeLayout'] ?? $this->typographySafeLayout,
            ctaSafeSpacing: $overrides['ctaSafeSpacing'] ?? $this->ctaSafeSpacing,
            foodTexture: $overrides['foodTexture'] ?? $this->foodTexture,
            layoutStrategy: $overrides['layoutStrategy'] ?? $this->layoutStrategy,
            visualHierarchy: $overrides['visualHierarchy'] ?? $this->visualHierarchy,
            director: $overrides['director'] ?? $this->director,
            commercialDirectives: $overrides['commercialDirectives'] ?? $this->commercialDirectives,
            metadata: array_merge($this->metadata, $overrides['metadata'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'hero_focus' => $this->heroFocus,
            'composition_type' => $this->compositionType,
            'campaign_visual_format' => $this->campaignVisualFormat,
            'realism_level' => $this->realismLevel,
            'photography_style' => $this->photographyStyle,
            'camera_angle' => $this->cameraAngle,
            'lighting' => $this->lighting,
            'negative_space' => $this->negativeSpace,
            'typography_safe_layout' => $this->typographySafeLayout,
            'cta_safe_spacing' => $this->ctaSafeSpacing,
            'food_texture' => $this->foodTexture,
            'layout_strategy' => $this->layoutStrategy,
            'visual_hierarchy' => $this->visualHierarchy,
            'director' => $this->director,
            'commercial_directives' => $this->commercialDirectives,
            'metadata' => $this->metadata,
        ];
    }
}
