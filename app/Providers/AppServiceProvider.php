<?php

declare(strict_types=1);

namespace App\Providers;

use App\Ai\Contracts\ImagePredictionProvider;
use App\Ai\Contracts\ImageProvider;
use App\Ai\Providers\ReplicateProvider;
use App\Modules\Generation\Services\GeminiAiService;
use App\Shared\Contracts\AiServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bind interfaces to concrete implementations.
     */
    public function register(): void
    {
        // Bind AiServiceInterface → GeminiAiService (swap easily for other providers)
        $this->app->bind(AiServiceInterface::class, GeminiAiService::class);
        $this->app->bind(ImageProvider::class, ReplicateProvider::class);
        $this->app->bind(ImagePredictionProvider::class, ReplicateProvider::class);
    }

    public function boot(): void
    {
        //
    }
}
