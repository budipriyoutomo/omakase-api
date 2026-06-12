<?php

declare(strict_types=1);

namespace App\Ai\Creative\Themes\Contracts;

use App\Ai\Creative\Tokens\DesignTokens;

interface ThemeInterface
{
    /**
     * Unique slug identifier untuk theme ini.
     * Contoh: "luxury_japanese", "modern_cafe"
     */
    public function slug(): string;

    /**
     * Nama human-readable theme.
     */
    public function name(): string;

    /**
     * Design tokens milik theme ini.
     */
    public function tokens(): DesignTokens;

    /**
     * Keywords yang merepresentasikan theme ini.
     * Dipakai ThemeDetectorAgent untuk AI matching.
     */
    public function keywords(): array;

    /**
     * Default layout mode untuk theme ini.
     */
    public function defaultLayoutMode(): string;

    /**
     * Serialize ke array.
     */
    public function toArray(): array;
}