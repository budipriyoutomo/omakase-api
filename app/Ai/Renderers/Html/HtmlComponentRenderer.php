<?php

declare(strict_types=1);

namespace App\Ai\Renderers\Html;

use App\Ai\Renderers\Support\CssPropertyBuilder;

final class HtmlComponentRenderer
{
    public function render(array $component): string
    {
        return match ($component['type']) {
            'hero_headline'          => $this->renderHeroHeadline($component),
            'sub_headline'           => $this->renderSubHeadline($component),
            'luxury_cta'             => $this->renderLuxuryCta($component),
            'launch_badge'           => $this->renderLaunchBadge($component),
            'japanese_vertical_text' => $this->renderJapaneseVerticalText($component),
            'editorial_divider'      => $this->renderEditorialDivider($component),
            'reservation_badge'      => $this->renderReservationBadge($component),
            default                  => '',
        };
    }

    public function renderAll(array $components): string
    {
        return collect($components)
            ->map(fn (array $component) => $this->render($component))
            ->filter()
            ->implode("\n");
    }

    /*
    |--------------------------------------------------------------------------
    | COMPONENT RENDERERS
    |--------------------------------------------------------------------------
    */

    private function renderHeroHeadline(array $c): string
    {
        $css = $this->basePositionCss($c, zIndex: 10);
        $css .= '; ' . CssPropertyBuilder::fromArray($c['styles']);

        $text = e($c['content']);

        return <<<HTML
        <h1 class="creative-headline" style="{$css}">{$text}</h1>
        HTML;
    }

    private function renderSubHeadline(array $c): string
    {
        if (empty($c['content'])) {
            return '';
        }

        $css = $this->basePositionCss($c, zIndex: 10);
        $css .= '; ' . CssPropertyBuilder::fromArray($c['styles']);

        $text = e($c['content']);

        return <<<HTML
        <h2 class="creative-subheadline" style="{$css}">{$text}</h2>
        HTML;
    }

    private function renderLuxuryCta(array $c): string
    {
        if (empty($c['content'])) {
            return '';
        }

        $css = $this->basePositionCss($c, zIndex: 10);
        $css .= '; ' . CssPropertyBuilder::fromArray($c['styles']);
        $css .= '; cursor: pointer; display: inline-block';

        $text = e($c['content']);

        return <<<HTML
        <div class="creative-cta" style="{$css}">{$text}</div>
        HTML;
    }

    private function renderLaunchBadge(array $c): string
    {
        if (empty($c['content'])) {
            return '';
        }

        $css = $this->basePositionCss($c, zIndex: 11);
        $css .= '; ' . CssPropertyBuilder::fromArray($c['styles']);
        $css .= '; display: inline-block';

        $text = e($c['content']);

        return <<<HTML
        <div class="creative-badge" style="{$css}">{$text}</div>
        HTML;
    }

    private function renderJapaneseVerticalText(array $c): string
    {
        if (empty($c['content'])) {
            return '';
        }

        $css = $this->basePositionCss($c, zIndex: 9);
        $css .= '; ' . CssPropertyBuilder::fromArray($c['styles']);

        $text = e($c['content']);

        return <<<HTML
        <div class="creative-jp-text" style="{$css}">{$text}</div>
        HTML;
    }

    private function renderEditorialDivider(array $c): string
    {
        $css = $this->basePositionCss($c, zIndex: 10);
        $css .= '; ' . CssPropertyBuilder::fromArray($c['styles']);

        return <<<HTML
        <div class="creative-divider" style="{$css}"></div>
        HTML;
    }

    private function renderReservationBadge(array $c): string
    {
        if (empty($c['content'])) {
            return '';
        }

        $css = $this->basePositionCss($c, zIndex: 11);
        $css .= '; ' . CssPropertyBuilder::fromArray($c['styles']);
        $css .= '; display: inline-block';

        $text = e($c['content']);

        return <<<HTML
        <div class="creative-reservation" style="{$css}">{$text}</div>
        HTML;
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    private function basePositionCss(array $c, int $zIndex = 10): string
    {
        $x = $c['position']['x'] ?? 0;
        $y = $c['position']['y'] ?? 0;

        return CssPropertyBuilder::fromArray([
            'position' => 'absolute',
            'left'     => $x . 'px',
            'top'      => $y . 'px',
            'z-index'  => (string) $zIndex,
        ]);
    }
}