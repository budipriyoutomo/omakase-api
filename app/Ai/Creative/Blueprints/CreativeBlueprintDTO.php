<?php

declare(strict_types=1);

namespace App\Ai\Creative\Blueprints;

final class CreativeBlueprintDTO
{
    public function __construct(
        public readonly string $theme,
        public readonly string $layoutMode,
        public readonly array  $canvas,
        public readonly array  $tokens,
        public readonly array  $components,
        public readonly array  $overlayStrategy,
    ) {}

    public static function assemble(
        string $theme,
        string $layoutMode,
        array  $canvas,
        array  $tokens,
        array  $components,
        array  $overlayStrategy,
    ): self {
        return new self(
            theme:           $theme,
            layoutMode:      $layoutMode,
            canvas:          $canvas,
            tokens:          $tokens,
            components:      $components,
            overlayStrategy: $overlayStrategy,
        );
    }

    public function toArray(): array
    {
        return [
            'theme'            => $this->theme,
            'layout_mode'      => $this->layoutMode,
            'canvas'           => $this->canvas,
            'tokens'           => $this->tokens,
            'components'       => $this->components,
            'overlay_strategy' => $this->overlayStrategy,
        ];
    }
}