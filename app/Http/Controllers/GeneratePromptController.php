<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Ai\DTOs\CampaignPayloadDTO;

use App\Ai\Services\PromptGenerationService;

class GeneratePromptController
{
    public function __invoke(

        Request $request,

        PromptGenerationService $service

    ) {

        $dto = new CampaignPayloadDTO(

            campaignType:
                $request->campaign_type,

            cuisine:
                $request->cuisine,

            platform:
                $request->platform,

            audience:
                $request->audience,

            goal:
                $request->goal,

            mood:
                $request->mood,

            style:
                $request->style,

            heroItem:
                $request->hero_item,

            visualStrategy:
                $request->visual_strategy,

            ctaStrategy:
                $request->cta_strategy,

            aspectRatio:
                $request->aspect_ratio,

            prompt:
                $request->prompt,

            negativePrompt:
                $request->negative_prompt,
        );

        $result = $service->generate($dto);

        return response()->json($result);
    }
}