<?php

declare(strict_types=1);

namespace App\Ai\Creative\Tokens;

final class ShadowToken
{
    public function __construct(
        public readonly string $textShadow,
        public readonly string $headlineShadow,
        public readonly string $ctaShadow,
        public readonly string $badgeShadow,
    ) {}

    public function toArray(): array
    {
        return [
            'text_shadow'     => $this->textShadow,
            'headline_shadow' => $this->headlineShadow,
            'cta_shadow'      => $this->ctaShadow,
            'badge_shadow'    => $this->badgeShadow,
        ];
    }
}