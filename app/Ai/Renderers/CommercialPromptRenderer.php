<?php

declare(strict_types=1);

namespace App\Ai\Renderers;

use App\Ai\Campaign\DTOs\CampaignIntelligenceDTO;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\DTOs\PromptRenderResultDTO;
use App\Ai\Visual\DTOs\VisualIntelligenceDTO;

final class CommercialPromptRenderer
{
    public const VERSION = 'commercial-visual-script-v1';

    public function render(
        CampaignPayloadDTO $payload,
        CampaignIntelligenceDTO $campaign,
        VisualIntelligenceDTO $visual
    ): PromptRenderResultDTO {
        $heroItem = filled($payload->heroItem)
            ? $payload->heroItem
            : 'the signature '.($payload->cuisine ?: 'restaurant').' hero dish';

        $prompt = implode("\n", array_filter([
            'COMMERCIAL RESTAURANT VISUAL DIRECTING SCRIPT',
            '',
            "Create a production-grade AI image for a restaurant campaign featuring {$heroItem}.",
            $payload->prompt ? "User creative brief: {$payload->prompt}" : null,
            '',
            'Campaign psychology:',
            "- campaign goal: {$campaign->campaignGoal}",
            "- conversion priority: {$campaign->conversionPriority}",
            "- brand positioning: {$campaign->brandPositioning}",
            "- audience energy: {$campaign->audienceEnergy}",
            "- platform behavior: {$campaign->platformBehavior}",
            "- campaign tone: {$campaign->campaignTone}",
            '',
            'Visual direction:',
            "- hero focus: {$visual->heroFocus}",
            "- composition type: {$visual->compositionType}",
            "- campaign visual format: {$visual->campaignVisualFormat}",
            "- realism level: {$visual->realismLevel}",
            "- photography style: {$visual->photographyStyle}",
            "- camera angle: {$visual->cameraAngle}",
            "- lighting: {$visual->lighting}",
            "- negative space: {$visual->negativeSpace}",
            "- typography-safe layout: {$visual->typographySafeLayout}",
            "- CTA-safe spacing: {$visual->ctaSafeSpacing}",
            "- food texture: {$visual->foodTexture}",
            "- layout strategy: {$visual->layoutStrategy}",
            "- visual hierarchy: {$visual->visualHierarchy}",
            '',
            'Commercial art direction:',
            $this->renderList($visual->commercialDirectives),
            '',
            'Final image requirements:',
            '- mobile-first restaurant advertising composition',
            '- appetizing macro realism, realistic ingredient scale, natural shadows',
            '- no readable text rendered inside the image unless explicitly requested',
            '- leave clean negative space for typography and CTA overlay in post-production',
            '- polished commercial photography, premium color grading, high detail, no clutter',
            $payload->aspectRatio ? "- target aspect ratio: {$payload->aspectRatio}" : null,
        ]));

        return new PromptRenderResultDTO(
            prompt: $prompt,
            negativePrompt: $this->negativePrompt($payload),
            renderer: self::class,
            version: self::VERSION,
            metadata: [
                'prompt_type' => 'visual_directing_script',
                'renderer_version' => self::VERSION,
                'supports_variants' => true,
                'supports_prompt_versioning' => true,
                'reserved_overlay_strategy' => [
                    'negative_space' => $visual->negativeSpace,
                    'cta_safe_spacing' => $visual->ctaSafeSpacing,
                    'typography_safe_layout' => $visual->typographySafeLayout,
                ],
            ],
        );
    }

    private function negativePrompt(CampaignPayloadDTO $payload): string
    {
        $defaults = [
            'deformed food',
            'fake ingredients',
            'plastic texture',
            'messy composition',
            'unreadable text',
            'random letters',
            'bad typography',
            'extra plates blocking hero dish',
            'low resolution',
            'oversaturated artificial colors',
            'warped utensils',
            'AI artifacts',
        ];

        if (filled($payload->negativePrompt)) {
            $defaults[] = $payload->negativePrompt;
        }

        return implode(', ', array_unique($defaults));
    }

    private function renderList(array $items): string
    {
        return collect($items)
            ->map(fn (string $item): string => "- {$item}")
            ->implode("\n");
    }
}
