<?php

declare(strict_types=1);

namespace App\Ai\DTOs;

final readonly class FoodEnrichmentDTO
{
    public function __construct(
        public bool $enriched,
        public string $source,           // 'knowledge_base' | 'gemini' | 'passthrough'
        public ?string $foodCategory,
        public array $visualKeywords,
        public array $textureDescriptors,
        public string $lightingPreset,
        public string $cameraAngle,
        public string $platingStyle,
        public array $colorPalette,
        public array $avoidElements,
        public array $cuisineProps,
        public array $campaignMood,
    ) {}

    public static function empty(): self
    {
        return new self(
            enriched:           false,
            source:             'passthrough',
            foodCategory:       null,
            visualKeywords:     [],
            textureDescriptors: [],
            lightingPreset:     '',
            cameraAngle:        '',
            platingStyle:       '',
            colorPalette:       [],
            avoidElements:      [],
            cuisineProps:       [],
            campaignMood:       [],
        );
    }

    public static function fromKnowledgeBase(array $data): self
    {
        return new self(
            enriched:           true,
            source:             'knowledge_base',
            foodCategory:       $data['food_category'] ?? null,
            visualKeywords:     $data['visual_keywords'] ?? [],
            textureDescriptors: $data['texture_descriptors'] ?? [],
            lightingPreset:     $data['lighting_preset'] ?? '',
            cameraAngle:        $data['camera_angle'] ?? '',
            platingStyle:       $data['plating_style'] ?? '',
            colorPalette:       $data['color_palette'] ?? [],
            avoidElements:      $data['avoid'] ?? [],
            cuisineProps:       $data['cuisine_props'] ?? [],
            campaignMood:       $data['campaign_mood'] ?? [],
        );
    }

    /**
     * Flatten visual keywords to a comma-separated string
     * for use inside director agent prompts.
     */
    public function toVisualContext(): string
    {
        if (! $this->enriched) {
            return '';
        }

        $parts = array_filter([
            ! empty($this->visualKeywords)
                ? implode(', ', array_slice($this->visualKeywords, 0, 3))
                : '',
            ! empty($this->textureDescriptors)
                ? implode(', ', array_slice($this->textureDescriptors, 0, 2))
                : '',
            $this->lightingPreset,
            $this->platingStyle,
        ]);

        return implode('. ', $parts);
    }

    public function toNegativeContext(): string
    {
        return implode(', ', $this->avoidElements);
    }

    public function toArray(): array
    {
        return [
            'enriched'            => $this->enriched,
            'source'              => $this->source,
            'food_category'       => $this->foodCategory,
            'visual_keywords'     => $this->visualKeywords,
            'texture_descriptors' => $this->textureDescriptors,
            'lighting_preset'     => $this->lightingPreset,
            'camera_angle'        => $this->cameraAngle,
            'plating_style'       => $this->platingStyle,
            'color_palette'       => $this->colorPalette,
            'avoid_elements'      => $this->avoidElements,
            'cuisine_props'       => $this->cuisineProps,
            'campaign_mood'       => $this->campaignMood,
        ];
    }
}