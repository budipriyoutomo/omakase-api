<?php

declare(strict_types=1);

namespace App\Modules\Generation\DTOs;

final readonly class CreateGenerationDTO
{
    public function __construct(
        public int $userId,
        public string $campaignType,
        public string $platform,
        public string $style,
        public string $prompt,
        public ?string $cuisine = null,
        public ?string $audience = null,
        public ?string $goal = null,
        public ?string $mood = null,
        public ?string $heroItem = null,
        public ?string $visualStrategy = null,
        public ?string $ctaStrategy = null,
        public ?string $aspectRatio = null,
        public ?string $negativePrompt = null,
    ) {}

    public static function fromArray(int $userId, array $data): self
    {
        return new self(
            userId: $userId,
            campaignType: $data['campaignType'],
            platform: $data['platform'],
            style: $data['style'],
            prompt: $data['prompt'],
            cuisine: $data['cuisine'] ?? null,
            audience: $data['audience'] ?? null,
            goal: $data['goal'] ?? null,
            mood: $data['mood'] ?? null,
            heroItem: $data['heroItem'] ?? null,
            visualStrategy: $data['visualStrategy'] ?? null,
            ctaStrategy: $data['ctaStrategy'] ?? null,
            aspectRatio: $data['aspectRatio'] ?? null,
            negativePrompt: $data['negativePrompt'] ?? null,
        );
    }
}
