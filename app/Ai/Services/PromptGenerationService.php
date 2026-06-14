<?php

declare(strict_types=1);

namespace App\Ai\Services;

use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\DTOs\ImageGenerationPayloadDTO;
use App\Ai\Orchestrators\PromptOrchestrator;

class PromptGenerationService
{
    public function __construct(
        protected PromptOrchestrator $orchestrator,
    ) {}

    /**
     * @deprecated Gunakan generateForImage() untuk generate gambar murni.
     */
    public function generate(CampaignPayloadDTO $dto): array
    {
        $lifecycle = $this->orchestrator->orchestrate($dto);

        return $this->formatResult($lifecycle);
    }

    /**
     * Generate enhanced prompt khusus untuk image generation.
     * Hanya menggunakan visual fields — TANPA campaign intelligence / marketing fields.
     */
    public function generateForImage(ImageGenerationPayloadDTO $dto): array
    {
        $lifecycle = $this->orchestrator->orchestrateForImage($dto);

        return $this->formatResult($lifecycle);
    }

    private function formatResult($lifecycle): array
    {
        return [
            'agent' => $lifecycle->visualIntelligence->director,
            'provider' => 'internal-renderer',
            'model' => $lifecycle->promptRender->version,
            'usage' => [
                'prompt_tokens' => 0,
                'completion_tokens' => 0,
                'reasoning_tokens' => 0,
            ],
            'orchestration' => $lifecycle->toArray(),
            'enhanced_prompt' => $lifecycle->promptRender->prompt,
            'negative_prompt' => $lifecycle->promptRender->negativePrompt,
            'raw_response' => json_encode(
                $lifecycle->toArray(),
                JSON_THROW_ON_ERROR
            ),
        ];
    }
}
