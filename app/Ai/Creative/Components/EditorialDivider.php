<?php

declare(strict_types=1);

namespace App\Ai\Creative\Components;

use App\Ai\Creative\Components\Contracts\ComponentInterface;
use App\Ai\Creative\Tokens\DesignTokens;

final class EditorialDivider implements ComponentInterface
{
    public function __construct(
        private readonly int    $x,
        private readonly int    $y,
        private readonly int    $width,
        private readonly bool   $visible = true,
    ) {}

    public function type(): string
    {
        return 'editorial_divider';
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function render(DesignTokens $tokens): array
    {
        return [
            'type'     => $this->type(),
            'content'  => null,
            'position' => [
                'x' => $this->x,
                'y' => $this->y,
            ],
            'styles'   => [
                'width'            => $this->width . 'px',
                'height'           => '1px',
                'background_color' => $tokens->colors->accent,
                'opacity'          => '0.4',
            ],
        ];
    }
}