<?php

declare(strict_types=1);

namespace App\Ai\Renderers\Html;

use App\Ai\Creative\Blueprints\CreativeBlueprintDTO;
use App\Ai\Renderers\Support\CssPropertyBuilder;

final class HtmlCanvasRenderer
{
    public function render(
        CreativeBlueprintDTO $blueprint,
        string $imageUrl,
        string $innerHtml,
    ): string {
        $canvas  = $blueprint->canvas;
        $tokens  = $blueprint->tokens;

        $width  = $canvas['width']  ?? 1080;
        $height = $canvas['height'] ?? 1080;

        $canvasCss = CssPropertyBuilder::fromArray([
            'position'              => 'relative',
            'width'                 => $width . 'px',
            'height'                => $height . 'px',
            'overflow'              => 'hidden',
            'background-image'      => "url('{$imageUrl}')",
            'background-size'       => 'cover',
            'background-position'   => 'center center',
            'background-repeat'     => 'no-repeat',
            'font-family'           => $tokens['typography']['body_font'] ?? 'sans-serif',
        ]);

        return <<<HTML
        <div class="creative-canvas" style="{$canvasCss}">
            {$innerHtml}
        </div>
        HTML;
    }
}