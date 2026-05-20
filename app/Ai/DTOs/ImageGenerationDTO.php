<?php

namespace App\Ai\DTOs;

class ImageGenerationDTO
{
    public function __construct(

        public readonly string $prompt,

        public readonly ?string $negativePrompt,

        public readonly string $aspectRatio,

        public readonly int $numImages = 1,
    ) {}
}
