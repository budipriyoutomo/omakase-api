<?php

declare(strict_types=1);

namespace App\Ai\Orchestrators;

use App\Ai\Campaign\DTOs\CampaignIntelligenceDTO;
use App\Ai\Contracts\VisualDirector;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\Routers\VisualDirectionRouter;
use App\Ai\Visual\Composition\VisualCompositionEngine;
use App\Ai\Visual\DTOs\VisualIntelligenceDTO;

final class VisualCompositionOrchestrator
{
    public function __construct(
        private readonly VisualCompositionEngine $compositionEngine,
        private readonly VisualDirectionRouter $router,
    ) {}

    public function build(
        CampaignPayloadDTO $payload,
        CampaignIntelligenceDTO $campaign
    ): VisualIntelligenceDTO {
        $visual = $this->compositionEngine->compose($payload, $campaign);
        $directorClass = $this->router->resolve($visual, $campaign, $payload);

        /** @var VisualDirector $director */
        $director = app($directorClass);

        return $director->refine(
            $visual->withOverrides([
                'director' => class_basename($directorClass),
            ]),
            $campaign,
            $payload
        );
    }
}
