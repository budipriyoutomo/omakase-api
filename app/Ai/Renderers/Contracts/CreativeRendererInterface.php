<?php

declare(strict_types=1);

namespace App\Ai\Renderers\Contracts;

use App\Ai\Creative\Blueprints\CreativeBlueprintDTO;

interface CreativeRendererInterface
{
    public function render(
        CreativeBlueprintDTO $blueprint,
        string $imageUrl,
    ): string;
}