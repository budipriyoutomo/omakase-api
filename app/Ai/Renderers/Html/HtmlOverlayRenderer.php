<?php

declare(strict_types=1);

namespace App\Ai\Renderers\Html;

use App\Ai\Creative\Blueprints\CreativeBlueprintDTO;
use App\Ai\Renderers\Support\CssPropertyBuilder;

final class HtmlOverlayRenderer
{
    public function render(CreativeBlueprintDTO $blueprint): string
    {
        $gradient = $blueprint->overlayStrategy['overlay_gradient']
            ?? 'linear-gradient(180deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.0) 60%)';

        $overlayCss = CssPropertyBuilder::fromArray([
            'position'   => 'absolute',
            'inset'      => '0',
            'background' => $gradient,
            'z-index'    => '1',
            'pointer-events' => 'none',
        ]);

        return <<<HTML
        <div class="creative-overlay" style="{$overlayCss}"></div>
        HTML;
    }
}