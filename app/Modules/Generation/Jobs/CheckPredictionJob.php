<?php

declare(strict_types=1);

namespace App\Modules\Generation\Jobs;

use App\Ai\Contracts\ImagePredictionProvider;
use App\Ai\Pipelines\StoragePipeline;
use App\Models\Generation;
use App\Modules\Generation\Events\GenerationCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckPredictionJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public int $tries = 20;

    public int $backoff = 10;

    public function __construct(
        private readonly string $generationId
    ) {
        $this->onQueue('ai-polling');
    }

    public function handle(
        ImagePredictionProvider $predictionProvider,
        StoragePipeline $storagePipeline
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
        | GET PREDICTION ID
        |--------------------------------------------------------------------------
        */

        $predictionId =
            data_get(
                $generation->metadata,
                'replicate_prediction_id'
            );

        if (! $predictionId) {

            Log::warning(
                'Missing Replicate prediction ID',
                [
                    'generation_id' => $generation->id,
                ]
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK REPLICATE STATUS
        |--------------------------------------------------------------------------
        */

        $providerResponse = $predictionProvider
            ->prediction($predictionId);

        if (! $providerResponse['successful']) {

            Log::error(
                'Failed checking Replicate prediction',
                [
                    'generation_id' => $generation->id,

                    'response' => $providerResponse['payload'],
                ]
            );

            $this->release(10);

            return;
        }

        $prediction =
            $providerResponse['payload'];

        $status =
            $prediction['status'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | STILL PROCESSING
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $status,
                [
                    'starting',
                    'processing',
                ]
            )
        ) {

            Log::info(
                'Replicate still processing',
                [
                    'generation_id' => $generation->id,

                    'prediction_id' => $predictionId,
                ]
            );

            $this->release(10);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | FAILED
        |--------------------------------------------------------------------------
        */

        if ($status === 'failed') {

            $generation->update([

                'status' => Generation::STATUS_FAILED,

                'ai_metadata' => array_merge(
                    $generation->ai_metadata ?? [],
                    [

                        'replicate_result' => $prediction,
                    ]
                ),
            ]);

            Log::error(
                'Replicate prediction failed',
                [
                    'generation_id' => $generation->id,

                    'prediction' => $prediction,
                ]
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | COMPLETED
        |--------------------------------------------------------------------------
        */

        if ($status === 'succeeded') {

            $output =
                $prediction['output'] ?? [];

            /*
            |--------------------------------------------------------------------------
            | STORE IMAGE TO S3
            |--------------------------------------------------------------------------
            */
            try {

                $storedImages = $storagePipeline
                    ->storeMany($output);

            } catch (Throwable $th) {
                Log::error(
                    'Failed storing AI image',
                    [
                        'generation_id' => $generation->id,

                        'error' => $th->getMessage(),
                    ]
                );

                $this->release(10);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | SAVE IMAGE
            |--------------------------------------------------------------------------
            */

            $generation->update([

                'status' => Generation::STATUS_COMPLETED,

                'image_url' => $storedImages[0] ?? null,

                'image_urls' => $storedImages,

                'ai_metadata' => array_merge(
                    $generation->ai_metadata ?? [],
                    [
                        'replicate_result' => $prediction,
                    ]
                ),
                'metadata' => array_merge(
                    $generation->metadata ?? [],
                    [

                        'image_storage_provider' => 's3',

                        'generated_at' => now()->toIso8601String(),

                        'image_count' => count($storedImages),

                        'replicate_status' => $status,
                    ]
                ),
            ]);

            /*
            |--------------------------------------------------------------------------
            | DISPATCH EVENT
            |--------------------------------------------------------------------------
            */

            GenerationCompleted::dispatch(
                $generation->fresh()
            );

            Log::info(
                'Generation completed',
                [
                    'generation_id' => $generation->id,
                ]
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | UNKNOWN STATUS
        |--------------------------------------------------------------------------
        */

        Log::warning(
            'Unknown Replicate status',
            [
                'generation_id' => $generation->id,

                'status' => $status,
            ]
        );

        $this->release(10);
    }

    public function failed(
        Throwable $exception
    ): void {

        Log::critical(
            'CheckPredictionJob permanently failed',
            [

                'generation_id' => $this->generationId,

                'error' => $exception->getMessage(),
            ]
        );

        Generation::where(
            'id',
            $this->generationId
        )->update([

            'status' => Generation::STATUS_FAILED,
        ]);
    }
}
