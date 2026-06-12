<?php

declare(strict_types=1);

namespace App\Ai\Creative\Tokens;

final class ButtonToken
{
    public function __construct(
        public readonly string $borderRadius,
        public readonly string $paddingX,
        public readonly string $paddingY,
        public readonly string $fontSize,
        public readonly string $fontWeight,
        public readonly string $letterSpacing,
        public readonly string $borderWidth,
        public readonly string $borderColor,
        public readonly string $textTransform,
    ) {}

    public function toArray(): array
    {
        return [
            'border_radius'  => $this->borderRadius,
            'padding_x'      => $this->paddingX,
            'padding_y'      => $this->paddingY,
            'font_size'      => $this->fontSize,
            'font_weight'    => $this->fontWeight,
            'letter_spacing' => $this->letterSpacing,
            'border_width'   => $this->borderWidth,
            'border_color'   => $this->borderColor,
            'text_transform' => $this->textTransform,
        ];
    }
}