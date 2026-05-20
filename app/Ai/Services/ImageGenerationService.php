<?php

declare(strict_types=1);

namespace App\Ai\Services;

use App\Ai\Contracts\ImageProvider;
use App\Ai\DTOs\ImageGenerationDTO;

class ImageGenerationService
{
    public function __construct(
        protected ImageProvider $provider,
    ) {}

    public function generate(ImageGenerationDTO $dto): array
    {
        return $this->provider->generate($dto);
    }
}
