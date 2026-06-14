<?php

declare(strict_types=1);

namespace App\Ai\Orchestrators;

use App\Ai\Campaign\DTOs\CampaignIntelligenceDTO;
use App\Ai\Contracts\VisualDirector;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\DTOs\FoodEnrichmentDTO;
use App\Ai\Routers\VisualDirectionRouter;
use App\Ai\Visual\Composition\VisualCompositionEngine;
use App\Ai\Visual\DTOs\VisualIntelligenceDTO;
use App\Ai\Visual\Support\FoodEnrichmentResolver;

final class VisualCompositionOrchestrator
{
    public function __construct(
        private readonly VisualCompositionEngine $compositionEngine,
        private readonly VisualDirectionRouter $router,
        private readonly FoodEnrichmentResolver $foodEnrichmentResolver,
    ) {}

    public function build(
        CampaignPayloadDTO $payload,
        CampaignIntelligenceDTO $campaign
    ): VisualIntelligenceDTO {
        // ── Resolve food enrichment BEFORE director selection ──
        $foodEnrichment = $this->foodEnrichmentResolver->resolve(
            heroItem:     $payload->heroItem    ?? '',
            cuisineType:  $payload->cuisine     ?? '',
            campaignType: $payload->campaignType ?? '',
            visualStyle:  $payload->style       ?? '',
            mood:         $payload->mood        ?? '',
        );

        $visual = $this->compositionEngine->compose($payload, $campaign);
        $directorClass = $this->router->resolve($visual, $campaign, $payload);

        /** @var VisualDirector $director */
        $director = app($directorClass);

        // ── Pass food enrichment to director via metadata ──
        $visualWithDirector = $visual->withOverrides([
            'director' => class_basename($directorClass),
            'metadata' => [
                'food_enrichment' => $foodEnrichment,
            ],
        ]);

        $result = $director->refine(
            $visualWithDirector,
            $campaign,
            $payload
        );

        // ── Ensure food enrichment persists in the final metadata ──
        return $result->withOverrides([
            'metadata' => [
                'food_enrichment' => $foodEnrichment,
            ],
        ]);
    }
}
