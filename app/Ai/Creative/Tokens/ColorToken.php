<?php

declare(strict_types=1);

namespace App\Ai\Creative\Tokens;

final class ColorToken
{
    public function __construct(
        public readonly string $primary,
        public readonly string $secondary,
        public readonly string $accent,
        public readonly string $background,
        public readonly string $surface,
        public readonly string $textPrimary,
        public readonly string $textSecondary,
        public readonly string $textAccent,
        public readonly string $overlay,
    ) {}

    public function toArray(): array
    {
        return [
            'primary'        => $this->primary,
            'secondary'      => $this->secondary,
            'accent'         => $this->accent,
            'background'     => $this->background,
            'surface'        => $this->surface,
            'text_primary'   => $this->textPrimary,
            'text_secondary' => $this->textSecondary,
            'text_accent'    => $this->textAccent,
            'overlay'        => $this->overlay,
        ];
    }
}