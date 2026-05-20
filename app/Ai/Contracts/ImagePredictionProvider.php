<?php

declare(strict_types=1);

namespace App\Ai\Contracts;

interface ImagePredictionProvider
{
    public function prediction(string $predictionId): array;
}
