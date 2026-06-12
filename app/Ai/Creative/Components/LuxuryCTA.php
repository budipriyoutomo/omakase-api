<?php

declare(strict_types=1);

namespace App\Ai\Creative\Components;

use App\Ai\Creative\Components\Contracts\ComponentInterface;
use App\Ai\Creative\Tokens\DesignTokens;

final class LuxuryCTA implements ComponentInterface
{
    public function __construct(
        private readonly string $text,
        private readonly int    $x,
        private readonly int    $y,
        private readonly string $alignment = 'left',
    ) {}

    public function type(): string
    {
        return 'luxury_cta';
    }

    public function isVisible(): bool
    {
        return $this->text !== '';
    }

    public function render(DesignTokens $tokens): array
    {
        return [
            'type'     => $this->type(),
            'content'  => $this->text,
            'position' => [
                'x' => $this->x,
                'y' => $this->y,
            ],
            'styles'   => [
                'font_family'    => $tokens->typography->ctaFont,
                'font_size'      => $tokens->button->fontSize,
                'font_weight'    => $tokens->button->fontWeight,
                'letter_spacing' => $tokens->button->letterSpacing,
                'text_transform' => $tokens->button->textTransform,
                'color'          => $tokens->colors->textAccent,
                'border'         => $tokens->button->borderWidth . ' solid ' . $tokens->button->borderColor,
                'border_radius'  => $tokens->button->borderRadius,
                'padding'        => $tokens->button->paddingY . ' ' . $tokens->button->paddingX,
                'background'     => 'transparent',
                'box_shadow'     => $tokens->shadows->ctaShadow,
                'text_align'     => $this->alignment,
            ],
        ];
    }
}