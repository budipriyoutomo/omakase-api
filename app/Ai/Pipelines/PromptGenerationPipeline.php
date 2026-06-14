<?php

declare(strict_types=1);

namespace App\Ai\Pipelines;

use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\DTOs\ImageGenerationPayloadDTO;
use App\Ai\Services\PromptGenerationService;

final class PromptGenerationPipeline
{
    public function __construct(
        private readonly PromptGenerationService $promptGenerationService,
    ) {}

    /**
     * @deprecated Gunakan handleForImage() untuk generate gambar murni.
     */
    public function handle(CampaignPayloadDTO $payload): array
    {
        return $this->promptGenerationService->generate($payload);
    }

    /**
     * Generate enhanced prompt khusus untuk image generation.
     * Hanya menggunakan visual fields — TANPA campaign intelligence / marketing fields.
     */
    public function handleForImage(ImageGenerationPayloadDTO $payload): array
    {
        return $this->promptGenerationService->generateForImage($payload);
    }
}
