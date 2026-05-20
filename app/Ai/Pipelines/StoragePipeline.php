<?php

declare(strict_types=1);

namespace App\Ai\Pipelines;

use App\Ai\Services\ImageStorageService;

final class StoragePipeline
{
    public function __construct(
        private readonly ImageStorageService $imageStorageService,
    ) {}

    public function storeMany(array $urls): array
    {
        return collect($urls)
            ->map(fn (string $url): string => $this->imageStorageService->storeFromUrl($url))
            ->toArray();
    }
}
