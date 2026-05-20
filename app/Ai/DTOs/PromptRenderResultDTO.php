<?php

declare(strict_types=1);

namespace App\Ai\DTOs;

use App\Ai\Contracts\ArrayableData;

final class PromptRenderResultDTO implements ArrayableData
{
    public function __construct(
        public readonly string $prompt,
        public readonly string $negativePrompt,
        public readonly string $renderer,
        public readonly string $version,
        public readonly array $metadata = [],
    ) {}

    public function toArray(): array
    {
        return [
            'prompt' => $this->prompt,
            'negative_prompt' => $this->negativePrompt,
            'renderer' => $this->renderer,
            'version' => $this->version,
            'metadata' => $this->metadata,
        ];
    }
}
