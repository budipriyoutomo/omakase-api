<?php

declare(strict_types=1);

namespace App\Ai\DTOs;

use App\Models\Generation;

/**
 * DTO khusus untuk generate gambar — hanya berisi field visual/image.
 * Tidak mengandung field marketing (campaignType, audience, goal, ctaStrategy, visualStrategy, platform).
 */
final class ImageGenerationPayloadDTO
{
    public function __construct(
        public readonly string $heroItem,
        public readonly string $cuisine,
        public readonly string $style,
        public readonly string $mood,
        public readonly string $aspectRatio,
        public readonly ?string $prompt = null,
        public readonly ?string $negativePrompt = null,
    ) {}

    public static function fromGeneration(Generation $generation): self
    {
        return new self(
            heroItem:      $generation->hero_item      ?? '',
            cuisine:       $generation->cuisine        ?? '',
            style:         $generation->style          ?? '',
            mood:          $generation->mood           ?? '',
            aspectRatio:   $generation->aspect_ratio   ?? '',
            prompt:        $generation->prompt,
            negativePrompt: $generation->negative_prompt,
        );
    }

    public function toArray(): array
    {
        return [
            'hero_item'       => $this->heroItem,
            'cuisine'         => $this->cuisine,
            'style'           => $this->style,
            'mood'            => $this->mood,
            'aspect_ratio'    => $this->aspectRatio,
            'prompt'          => $this->prompt,
            'negative_prompt' => $this->negativePrompt,
        ];
    }
}