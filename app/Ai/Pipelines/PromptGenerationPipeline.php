<?php

declare(strict_types=1);

namespace App\Ai\Pipelines;

use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\Services\PromptGenerationService;

final class PromptGenerationPipeline
{
    public function __construct(
        private readonly PromptGenerationService $promptGenerationService,
    ) {}

    public function handle(CampaignPayloadDTO $payload): array
    {
        return $this->promptGenerationService->generate($payload);
    }
}
