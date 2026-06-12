<?php

declare(strict_types=1);

namespace App\Ai\Creative;

use App\Ai\Creative\Agents\ThemeDetectorAgent;
use App\Ai\Creative\Themes\Contracts\ThemeInterface;
use App\Ai\DTOs\CampaignPayloadDTO;
use Illuminate\Support\Facades\Log;

final class ThemeResolver
{
    public function __construct(
        private readonly ThemeRegistry $registry,
    ) {}

    public function resolve(
        CampaignPayloadDTO $dto,
        ?string $manualOverride = null,
    ): ThemeInterface {

        /*
        |--------------------------------------------------------------------------
        | MANUAL OVERRIDE
        |--------------------------------------------------------------------------
        */

        if ($manualOverride && $this->registry->has($manualOverride)) {

            Log::debug('ThemeResolver: manual override', [
                'theme' => $manualOverride,
            ]);

            return $this->registry->resolve($manualOverride);
        }

        /*
        |--------------------------------------------------------------------------
        | AI DETECTION
        |--------------------------------------------------------------------------
        */

        try {

            $response = (new ThemeDetectorAgent($dto, $this->registry))
                ->prompt('Detect the most fitting theme for this campaign.');

            $slug = trim($response->text);

            if ($this->registry->has($slug)) {

                Log::debug('ThemeResolver: AI detected theme', [
                    'theme' => $slug,
                ]);

                return $this->registry->resolve($slug);
            }

            Log::warning('ThemeResolver: AI returned unknown theme slug', [
                'slug' => $slug,
            ]);

        } catch (\Throwable $e) {

            Log::error('ThemeResolver: AI detection failed', [
                'error' => $e->getMessage(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | FALLBACK
        |--------------------------------------------------------------------------
        */

        Log::debug('ThemeResolver: using fallback theme');

        return $this->registry->fallback();
    }
}