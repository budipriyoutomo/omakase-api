<?php

declare(strict_types=1);

namespace App\Ai\Creative\Components;

use App\Ai\Creative\Components\Contracts\ComponentInterface;
use App\Ai\Creative\Tokens\DesignTokens;

final class LaunchBadge implements ComponentInterface
{
    public function __construct(
        private readonly string $text,
        private readonly int    $x,
        private readonly int    $y,
    ) {}

    public function type(): string
    {
        return 'launch_badge';
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
                'font_family'     => $tokens->typography->ctaFont,
                'font_size'       => '11px',
                'font_weight'     => '600',
                'letter_spacing'  => '0.15em',
                'text_transform'  => 'uppercase',
                'color'           => $tokens->colors->textAccent,
                'background'      => $tokens->gradients->badgeGradient,
                'border'          => '1px solid ' . $tokens->colors->accent,
                'border_radius'   => '2px',
                'padding'         => '6px 16px',
                'box_shadow'      => $tokens->shadows->badgeShadow,
            ],
        ];
    }
}