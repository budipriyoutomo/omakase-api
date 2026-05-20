<?php

declare(strict_types=1);

namespace App\Ai\Services;

use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\Orchestrators\PromptOrchestrator;

class PromptGenerationService
{
    public function __construct(
        protected PromptOrchestrator $orchestrator,
    ) {}

    public function generate(CampaignPayloadDTO $dto): array
    {
        $lifecycle = $this->orchestrator->orchestrate($dto);

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
