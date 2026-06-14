<?php

declare(strict_types=1);

namespace App\Ai\Renderers;

use App\Ai\DTOs\ImageGenerationPayloadDTO;
use App\Ai\DTOs\PromptRenderResultDTO;
use App\Ai\Visual\DTOs\VisualIntelligenceDTO;

/**
 * Renderer prompt khusus untuk generate gambar.
 * Hanya menggunakan field visual (heroItem, cuisine, style, mood, aspectRatio, prompt, negativePrompt).
 * Tidak mengandung campaign intelligence / marketing fields.
 */
final class ImagePromptRenderer
{
    public const VERSION = 'image-only-visual-script-v2';

    public function render(
        ImageGenerationPayloadDTO $payload,
        VisualIntelligenceDTO $visual
    ): PromptRenderResultDTO {
        $heroItem = filled($payload->heroItem)
            ? $payload->heroItem
            : 'the signature ' . ($payload->cuisine ?: 'restaurant') . ' hero dish';

        $prompt = implode("\n", array_filter([
            'RESTAURANT FOOD IMAGE GENERATION SCRIPT',
            '',
            "Create a production-grade AI image for a restaurant featuring {$heroItem}.",
            $payload->prompt ? "User creative brief: {$payload->prompt}" : null,
            '',
            'Visual direction:',
            "- hero focus: {$visual->heroFocus}",
            "- composition type: {$visual->compositionType}",
            "- visual format: {$visual->campaignVisualFormat}",
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
            'Art direction directives:',
            $this->renderList($visual->commercialDirectives),
            '',
            'Final image requirements:',
            '- appetizing food photography, realistic ingredient scale, natural shadows',
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
                'prompt_type' => 'image_only_visual_script',
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

    private function negativePrompt(ImageGenerationPayloadDTO $payload): string
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