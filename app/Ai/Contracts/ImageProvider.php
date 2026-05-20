<?php

declare(strict_types=1);

namespace App\Ai\Contracts;

use App\Ai\DTOs\ImageGenerationDTO;

interface ImageProvider
{
    public function generate(ImageGenerationDTO $dto): array;
}
