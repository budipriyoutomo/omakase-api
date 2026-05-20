<?php

declare(strict_types=1);

namespace App\Ai\Contracts;

interface ArrayableData
{
    public function toArray(): array;
}
