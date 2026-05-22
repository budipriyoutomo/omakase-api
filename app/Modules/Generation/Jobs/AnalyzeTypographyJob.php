<?php

declare(strict_types=1);

namespace App\Modules\Generation\Jobs;

use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\Typography\Vision\TypographyVisionAnalyzer;
use App\Models\Generation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class AnalyzeTypographyJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public int $tries = 3;

    public int $backoff = 15;

    public function __construct(
        private readonly string $generationId
    ) {
        $this->onQueue('ai-typography');
    }

    public function handle(
        TypographyVisionAnalyzer $analyzer
    ): void {
        /*
        |--------------------------------------------------------------------------
        | LOAD GENERATION
        |--------------------------------------------------------------------------
        */

        $generation = Generation::find(
            $this->generationId
        );

        if (! $generation) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATE IMAGE URL
        |--------------------------------------------------------------------------
        */

        $imageUrl = $generation->image_url;

        if (! $imageUrl) {

            Log::warning(
                'AnalyzeTypographyJob: no image_url found',
                [
                    'generation_id' => $generation->id,
                ]
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | BUILD CAMPAIGN PAYLOAD
        |--------------------------------------------------------------------------
        */

        $dto = CampaignPayloadDTO::fromGeneration(
            $generation
        );

        /*
        |--------------------------------------------------------------------------
        | ANALYZE
        |--------------------------------------------------------------------------
        */

        $blueprint = $analyzer->analyze(
            $imageUrl,
            $dto
        );

        /*
        |--------------------------------------------------------------------------
        | SAVE RESULT
        |--------------------------------------------------------------------------
        */

        $generation->update([
            'ai_metadata' => array_merge(
                $generation->ai_metadata ?? [],
                [
                    'typography_blueprint' => $blueprint->toArray(),
                ]
            ),
        ]);

        Log::info(
            'AnalyzeTypographyJob: typography blueprint saved',
            [
                'generation_id' => $generation->id,
            ]
        );
    }

    public function failed(
        Throwable $exception
    ): void {

        Log::error(
            'AnalyzeTypographyJob permanently failed',
            [
                'generation_id' => $this->generationId,
                'error'         => $exception->getMessage(),
            ]
        );
    }
}