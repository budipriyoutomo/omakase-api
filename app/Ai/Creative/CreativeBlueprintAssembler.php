<?php

declare(strict_types=1);

namespace App\Ai\Creative;

use App\Ai\Creative\Blueprints\CreativeBlueprintDTO;
use App\Ai\Creative\Components\ComponentFactory;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\Typography\DTOs\TypographyBlueprintDTO;
use Illuminate\Support\Facades\Log;

final class CreativeBlueprintAssembler
{
    public function __construct(
        private readonly ThemeResolver    $themeResolver,
        private readonly ComponentFactory $componentFactory,
    ) {}

    public function assemble(
        CampaignPayloadDTO     $campaign,
        TypographyBlueprintDTO $typography,
        ?string                $manualTheme = null,
    ): CreativeBlueprintDTO {

        /*
        |--------------------------------------------------------------------------
        | RESOLVE THEME
        |--------------------------------------------------------------------------
        */

        $theme = $this->themeResolver->resolve(
            dto:            $campaign,
            manualOverride: $manualTheme,
        );

        /*
        |--------------------------------------------------------------------------
        | GET DESIGN TOKENS
        |--------------------------------------------------------------------------
        */

        $tokens = $theme->tokens();

        /*
        |--------------------------------------------------------------------------
        | RESOLVE CANVAS
        |--------------------------------------------------------------------------
        */

        $canvas = $this->resolveCanvas($campaign->aspectRatio);

        /*
        |--------------------------------------------------------------------------
        | BUILD COMPONENTS
        |--------------------------------------------------------------------------
        */

        $componentInstances = $this->componentFactory->build(
            typography: $typography,
            tokens:     $tokens,
            campaign:   $campaign,
        );

        /*
        |--------------------------------------------------------------------------
        | RENDER COMPONENTS
        |--------------------------------------------------------------------------
        */

        $renderedComponents = $this->componentFactory->render(
            components: $componentInstances,
            tokens:     $tokens,
        );

        /*
        |--------------------------------------------------------------------------
        | OVERLAY STRATEGY
        |--------------------------------------------------------------------------
        */

        $overlayStrategy = [
            'negative_space'         => $typography->headline['placement']    ?? 'top-left',
            'typography_safe_layout' => $typography->subheadline['placement'] ?? '',
            'layout_strategy'        => $typography->layoutStrategy           ?? '',
            'visual_reasoning'       => $typography->visualReasoning          ?? '',
            'overlay_gradient'       => $tokens->gradients->overlayGradient,
        ];

        /*
        |--------------------------------------------------------------------------
        | ASSEMBLE BLUEPRINT
        |--------------------------------------------------------------------------
        */

        $blueprint = CreativeBlueprintDTO::assemble(
            theme:           $theme->slug(),
            layoutMode:      $theme->defaultLayoutMode(),
            canvas:          $canvas,
            tokens:          $tokens->toArray(),
            components:      $renderedComponents,
            overlayStrategy: $overlayStrategy,
        );

        Log::info('CreativeBlueprintAssembler: blueprint assembled', [
            'theme'           => $theme->slug(),
            'layout_mode'     => $theme->defaultLayoutMode(),
            'component_count' => count($renderedComponents),
        ]);

        return $blueprint;
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE
    |--------------------------------------------------------------------------
    */

    private function resolveCanvas(string $aspectRatio): array
    {
        return match ($aspectRatio) {
            '1:1'  => ['width' => 1080, 'height' => 1080, 'aspect_ratio' => '1:1'],
            '4:5'  => ['width' => 1080, 'height' => 1350, 'aspect_ratio' => '4:5'],
            '9:16' => ['width' => 1080, 'height' => 1920, 'aspect_ratio' => '9:16'],
            '16:9' => ['width' => 1920, 'height' => 1080, 'aspect_ratio' => '16:9'],
            default => ['width' => 1080, 'height' => 1080, 'aspect_ratio' => '1:1'],
        };
    }
}