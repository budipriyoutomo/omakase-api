<?php

declare(strict_types=1);

namespace App\Ai\Creative\Components;

use App\Ai\Creative\Components\Contracts\ComponentInterface;
use App\Ai\Creative\Tokens\DesignTokens;

final class HeroHeadline implements ComponentInterface
{
    public function __construct(
        private readonly string $text,
        private readonly int    $x,
        private readonly int    $y,
        private readonly int    $width,
        private readonly string $alignment = 'left',
    ) {}

    public function type(): string
    {
        return 'hero_headline';
    }

    public function isVisible(): bool
    {
        return $this->text !== '';
    }

    public function render(DesignTokens $tokens): array
    {
        return [
            'type'      => $this->type(),
            'content'   => $this->text,
            'position'  => [
                'x' => $this->x,
                'y' => $this->y,
            ],
            'styles'    => [
                'font_family'    => $tokens->typography->headlineFont,
                'font_size'      => $tokens->typography->headlineSize,
                'font_weight'    => $tokens->typography->headlineWeight,
                'letter_spacing' => $tokens->typography->headlineLetterSpacing,
                'line_height'    => $tokens->typography->headlineLineHeight,
                'color'          => $tokens->colors->textPrimary,
                'text_shadow'    => $tokens->shadows->headlineShadow,
                'text_align'     => $this->alignment,
                'max_width'      => $this->width . 'px',
            ],
        ];
    }
}