<?php

declare(strict_types=1);

namespace App\Ai\Creative\Tokens;

final class SpacingToken
{
    public function __construct(
        public readonly string $xs,
        public readonly string $sm,
        public readonly string $md,
        public readonly string $lg,
        public readonly string $xl,
        public readonly string $xxl,
        public readonly string $canvasPadding,
        public readonly string $componentGap,
    ) {}

    public function toArray(): array
    {
        return [
            'xs'             => $this->xs,
            'sm'             => $this->sm,
            'md'             => $this->md,
            'lg'             => $this->lg,
            'xl'             => $this->xl,
            'xxl'            => $this->xxl,
            'canvas_padding' => $this->canvasPadding,
            'component_gap'  => $this->componentGap,
        ];
    }
}