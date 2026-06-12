<?php

declare(strict_types=1);

namespace App\Ai\Creative\Tokens;

final class GradientToken
{
    public function __construct(
        public readonly string $overlayGradient,
        public readonly string $backgroundGradient,
        public readonly string $ctaGradient,
        public readonly string $badgeGradient,
    ) {}

    public function toArray(): array
    {
        return [
            'overlay_gradient'    => $this->overlayGradient,
            'background_gradient' => $this->backgroundGradient,
            'cta_gradient'        => $this->ctaGradient,
            'badge_gradient'      => $this->badgeGradient,
        ];
    }
}