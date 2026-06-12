<?php

declare(strict_types=1);

namespace App\Ai\Creative\Components;

use App\Ai\Creative\Components\Contracts\ComponentInterface;
use App\Ai\Creative\Tokens\DesignTokens;

final class ReservationBadge implements ComponentInterface
{
    public function __construct(
        private readonly string $text,
        private readonly int    $x,
        private readonly int    $y,
        private readonly bool   $visible = true,
    ) {}

    public function type(): string
    {
        return 'reservation_badge';
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
                'font_family'    => $tokens->typography->ctaFont,
                'font_size'      => '10px',
                'font_weight'    => '400',
                'letter_spacing' => '0.18em',
                'text_transform' => 'uppercase',
                'color'          => $tokens->colors->textPrimary,
                'background'     => $tokens->colors->overlay,
                'border'         => '1px solid ' . $tokens->colors->accent,
                'border_radius'  => '1px',
                'padding'        => '8px 20px',
                'box_shadow'     => $tokens->shadows->badgeShadow,
                'backdrop_filter'=> 'blur(4px)',
            ],
        ];
    }
}