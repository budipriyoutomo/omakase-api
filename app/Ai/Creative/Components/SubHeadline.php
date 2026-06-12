<?php

declare(strict_types=1);

namespace App\Ai\Creative\Components;

use App\Ai\Creative\Components\Contracts\ComponentInterface;
use App\Ai\Creative\Tokens\DesignTokens;

final class SubHeadline implements ComponentInterface
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
        return 'sub_headline';
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
                'font_family'    => $tokens->typography->subheadlineFont,
                'font_size'      => $tokens->typography->subheadlineSize,
                'font_weight'    => '300',
                'letter_spacing' => '0.05em',
                'line_height'    => '1.4',
                'color'          => $tokens->colors->textSecondary,
                'text_shadow'    => $tokens->shadows->textShadow,
                'text_align'     => $this->alignment,
                'max_width'      => $this->width . 'px',
            ],
        ];
    }
}