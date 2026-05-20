<?php

declare(strict_types=1);

namespace App\Ai\Pipelines;

use App\Ai\DTOs\ImageGenerationDTO;
use App\Ai\Services\ImageGenerationService;

final class ImageGenerationPipeline
{
    public function __construct(
        private readonly ImageGenerationService $imageGenerationService,
    ) {}

    public function dispatch(ImageGenerationDTO $payload): array
    {
        return $this->imageGenerationService->generate($payload);
    }
}
