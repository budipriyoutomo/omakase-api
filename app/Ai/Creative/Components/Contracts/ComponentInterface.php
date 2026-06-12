<?php

declare(strict_types=1);

namespace App\Ai\Creative\Components\Contracts;

use App\Ai\Creative\Tokens\DesignTokens;

interface ComponentInterface
{
    /**
     * Tipe unik component.
     * Contoh: "hero_headline", "luxury_cta"
     */
    public function type(): string;

    /**
     * Render component menjadi structured array
     * siap dikonsumsi Phase 4 HTML/CSS Renderer.
     */
    public function render(DesignTokens $tokens): array;

    /**
     * Apakah component ini visible / perlu dirender.
     */
    public function isVisible(): bool;
}