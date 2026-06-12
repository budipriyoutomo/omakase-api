<?php

declare(strict_types=1);

namespace App\Ai\Renderers\Html;

use App\Ai\Creative\Blueprints\CreativeBlueprintDTO;
use App\Ai\Renderers\Contracts\CreativeRendererInterface;

final class HtmlCreativeRenderer implements CreativeRendererInterface
{
    public function __construct(
        private readonly HtmlCanvasRenderer    $canvasRenderer,
        private readonly HtmlOverlayRenderer   $overlayRenderer,
        private readonly HtmlComponentRenderer $componentRenderer,
    ) {}

    public function render(
        CreativeBlueprintDTO $blueprint,
        string $imageUrl,
    ): string {
        /*
        |--------------------------------------------------------------------------
        | RENDER OVERLAY
        |--------------------------------------------------------------------------
        */

        $overlay = $this->overlayRenderer->render($blueprint);

        /*
        |--------------------------------------------------------------------------
        | RENDER COMPONENTS
        |--------------------------------------------------------------------------
        */

        $components = $this->componentRenderer->renderAll(
            $blueprint->components
        );

        /*
        |--------------------------------------------------------------------------
        | ASSEMBLE INNER HTML
        |--------------------------------------------------------------------------
        */

        $innerHtml = $overlay . "\n" . $components;

        /*
        |--------------------------------------------------------------------------
        | RENDER CANVAS
        |--------------------------------------------------------------------------
        */

        $canvas = $this->canvasRenderer->render(
            blueprint: $blueprint,
            imageUrl:  $imageUrl,
            innerHtml: $innerHtml,
        );

        /*
        |--------------------------------------------------------------------------
        | WRAP IN FULL HTML DOCUMENT
        |--------------------------------------------------------------------------
        */

        return $this->wrapHtml(
            canvas:    $canvas,
            blueprint: $blueprint,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE
    |--------------------------------------------------------------------------
    */

    private function wrapHtml(
        string               $canvas,
        CreativeBlueprintDTO $blueprint,
    ): string {
        $tokens    = $blueprint->tokens;
        $bodyFont  = $tokens['typography']['body_font']     ?? 'sans-serif';
        $headFont  = $tokens['typography']['headline_font'] ?? 'serif';
        $googleFonts = $this->resolveGoogleFonts($bodyFont, $headFont);

        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="theme" content="{$blueprint->theme}">
            <meta name="layout" content="{$blueprint->layoutMode}">
            <title>Creative — {$blueprint->theme}</title>
            {$googleFonts}
            <style>
                *, *::before, *::after {
                    box-sizing: border-box;
                    margin: 0;
                    padding: 0;
                }
                body {
                    background: #0a0a0a;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                }
                .creative-canvas * {
                    -webkit-font-smoothing: antialiased;
                    -moz-osx-font-smoothing: grayscale;
                }
            </style>
        </head>
        <body>
            {$canvas}
        </body>
        </html>
        HTML;
    }

    private function resolveGoogleFonts(string ...$fonts): string
    {
        $knownFonts = [
            'Cormorant Garamond' => 'Cormorant+Garamond:wght@300;400;600',
            'Playfair Display'   => 'Playfair+Display:wght@400;700;900',
            'EB Garamond'        => 'EB+Garamond:wght@400;500',
            'Montserrat'         => 'Montserrat:wght@400;600;700;800;900',
            'Inter'              => 'Inter:wght@300;400;500;600',
            'Lato'               => 'Lato:wght@300;400;700',
            'Noto Serif JP'      => 'Noto+Serif+JP:wght@300;400',
            'Zen Kaku Gothic New'=> 'Zen+Kaku+Gothic+New:wght@300;400',
        ];

        $families = collect($fonts)
            ->unique()
            ->filter(fn ($f) => isset($knownFonts[$f]))
            ->map(fn ($f) => 'family=' . $knownFonts[$f])
            ->implode('&');

        if (! $families) {
            return '';
        }

        return <<<HTML
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?{$families}&display=swap" rel="stylesheet">
        HTML;
    }
}