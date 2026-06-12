<?php

declare(strict_types=1);

namespace App\Ai\Creative\Components;

use App\Ai\Creative\Components\Contracts\ComponentInterface;
use App\Ai\Creative\Tokens\DesignTokens;

final class JapaneseVerticalText implements ComponentInterface
{
    public function __construct(
        private readonly string $text,
        private readonly int    $x,
        private readonly int    $y,
        private readonly bool   $visible = true,
    ) {}

    public function type(): string
    {
        return 'japanese_vertical_text';
    }

    public function isVisible(): bool
    {
        return $this->visible && $this->text !== '';
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
                'font_family'      => $tokens->typography->accentFont,
                'font_size'        => '13px',
                'font_weight'      => '300',
                'letter_spacing'   => '0.2em',
                'color'            => $tokens->colors->textSecondary,
                'writing_mode'     => 'vertical-rl',
                'text_orientation' => 'mixed',
                'opacity'          => '0.6',
                'text_shadow'      => $tokens->shadows->textShadow,
            ],
        ];
    }
}
