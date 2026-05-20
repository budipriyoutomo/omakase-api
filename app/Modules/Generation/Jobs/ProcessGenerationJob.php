<?php

declare(strict_types=1);

namespace App\Modules\Generation\Jobs;

use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\DTOs\ImageGenerationDTO;
use App\Ai\Pipelines\AnalyticsPipeline;
use App\Ai\Pipelines\ImageGenerationPipeline;
use App\Ai\Pipelines\PromptGenerationPipeline;
use App\Models\Generation;
// use App\Modules\Generation\Events\GenerationCompleted;
use App\Modules\Generation\Events\VisualOrchestrationRendered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessGenerationJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        private readonly string $generationId
    ) {
        $this->onQueue('ai-generation');
    }

    public function handle(
        PromptGenerationPipeline $promptPipeline,
        ImageGenerationPipeline $imagePipeline,
        AnalyticsPipeline $analyticsPipeline,
    ): void {

        $generation =
            Generation::findOrFail(
                $this->generationId
            );

        /*
        |--------------------------------------------------------------------------
        | PREVENT DUPLICATE PROCESS
        |--------------------------------------------------------------------------
        */

        if (
            $generation->status !==
            Generation::STATUS_PENDING
        ) {
            return;
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS
            |--------------------------------------------------------------------------
            */

            $generation->update([
                'status' => Generation::STATUS_PROCESSING,
            ]);

            /*
            |--------------------------------------------------------------------------
            | BUILD DTO
            |--------------------------------------------------------------------------
            */

            $dto = new CampaignPayloadDTO(

                campaignType: $generation->campaign_type,

                cuisine: $generation->cuisine ?? '',

                platform: $generation->platform,

                audience: $generation->audience ?? '',

                goal: $generation->goal ?? '',

                mood: $generation->mood ?? '',

                style: $generation->style,

                heroItem: $generation->hero_item ?? '',

                visualStrategy: $generation->visual_strategy ?? '',

                ctaStrategy: $generation->cta_strategy ?? '',

                aspectRatio: $generation->aspect_ratio ?? '',

                prompt: $generation->prompt,

                negativePrompt: $generation->negative_prompt,
            );

            /*
            |--------------------------------------------------------------------------
            | GENERATE ENHANCED PROMPT
            |--------------------------------------------------------------------------
            */

            $result =
                $promptPipeline->handle(
                    $dto
                );

            VisualOrchestrationRendered::dispatch(
                $generation,
                $result['orchestration']
            );

            /*
            |--------------------------------------------------------------------------
            | GENERATE IMAGE
            |--------------------------------------------------------------------------
            */

            $imageResult = $imagePipeline->dispatch(
                new ImageGenerationDTO(
                    prompt: $result['enhanced_prompt'],

                    negativePrompt: $result['negative_prompt']
                        ?? $dto->negativePrompt,

                    aspectRatio: $dto->aspectRatio,

                    numImages: 1,
                )
            );

            $analyticsMetadata = $analyticsPipeline
                ->metadataForQueuedImage(
                    $generation,
                    $result,
                    $imageResult
                );

            /*
            |--------------------------------------------------------------------------
            | SAVE RESULT
            |--------------------------------------------------------------------------
            */

            $generation->update([
                'enhanced_prompt' => $result['enhanced_prompt'],

                'orchestration' => $result['orchestration'],

                'agent' => $result['agent'],

                'provider' => $result['provider'],

                'model' => $result['model'],

                'tokens_used' => (
                    $result['usage']['prompt_tokens']
                    +
                    $result['usage']['completion_tokens']
                ),

                'raw_response' => $result['raw_response'],

                'ai_metadata' => array_merge(
                    $generation->ai_metadata ?? [],
                    [
                        'replicate_prediction' => $imageResult,
                        'visual_orchestration_lifecycle' => $result['orchestration'],
                    ]
                ),

                'metadata' => array_merge(
                    $generation->metadata ?? [],
                    [
                        'replicate_prediction_id' => $imageResult['id'] ?? null,
                        'image_provider' => 'replicate',

                        'image_model' => 'flux-schnell',

                        'visual_ai' => $analyticsMetadata,
                    ]
                ),
                'status' => Generation::STATUS_GENERATED_IMAGE,
            ]);

            /*
            |--------------------------------------------------------------------------
            | EVENT
            |--------------------------------------------------------------------------
            */

            CheckPredictionJob::dispatch(
                $generation->id
            )->delay(now()->addSeconds(5));

        } catch (\Throwable $e) {

            $generation->update([

                'status' => Generation::STATUS_FAILED,

                'metadata' => array_merge(
                    $generation->metadata ?? [],
                    [

                        'error' => $e->getMessage(),

                        'failed_at' => now()->toIso8601String(),
                    ]
                ),
            ]);

            Log::error(
                'ProcessGenerationJob failed',
                [

                    'generation_id' => $this->generationId,

                    'error' => $e->getMessage(),
                ]
            );

            throw $e;
        }
    }

    public function failed(
        \Throwable $exception
    ): void {

        Log::critical(
            'ProcessGenerationJob permanently failed',
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
