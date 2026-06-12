<?php

declare(strict_types=1);

namespace App\Ai\Creative\Tokens;

final class TypographyToken
{
    public function __construct(
        public readonly string $headlineFont,
        public readonly string $subheadlineFont,
        public readonly string $bodyFont,
        public readonly string $ctaFont,
        public readonly string $accentFont,
        public readonly string $headlineSize,
        public readonly string $subheadlineSize,
        public readonly string $bodySize,
        public readonly string $ctaSize,
        public readonly string $headlineWeight,
        public readonly string $headlineLetterSpacing,
        public readonly string $headlineLineHeight,
    ) {}

    public function toArray(): array
    {
        return [
            'headline_font'           => $this->headlineFont,
            'subheadline_font'        => $this->subheadlineFont,
            'body_font'               => $this->bodyFont,
            'cta_font'                => $this->ctaFont,
            'accent_font'             => $this->accentFont,
            'headline_size'           => $this->headlineSize,
            'subheadline_size'        => $this->subheadlineSize,
            'body_size'               => $this->bodySize,
            'cta_size'                => $this->ctaSize,
            'headline_weight'         => $this->headlineWeight,
            'headline_letter_spacing' => $this->headlineLetterSpacing,
            'headline_line_height'    => $this->headlineLineHeight,
        ];
    }
}