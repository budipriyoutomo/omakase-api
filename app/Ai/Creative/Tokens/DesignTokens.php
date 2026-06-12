<?php

declare(strict_types=1);

namespace App\Ai\Creative\Tokens;

final class DesignTokens
{
    public function __construct(
        public readonly ColorToken      $colors,
        public readonly TypographyToken $typography,
        public readonly SpacingToken    $spacing,
        public readonly ShadowToken     $shadows,
        public readonly GradientToken   $gradients,
        public readonly ButtonToken     $button,
    ) {}

    public function toArray(): array
    {
        return [
            'colors'     => $this->colors->toArray(),
            'typography' => $this->typography->toArray(),
            'spacing'    => $this->spacing->toArray(),
            'shadows'    => $this->shadows->toArray(),
            'gradients'  => $this->gradients->toArray(),
            'button'     => $this->button->toArray(),
        ];
    }
}