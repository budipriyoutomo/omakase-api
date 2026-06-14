<?php

declare(strict_types=1);

namespace App\Modules\Generation\Jobs;

use App\Ai\DTOs\ImageGenerationDTO;
use App\Ai\DTOs\ImageGenerationPayloadDTO;
use App\Ai\DTOs\MarketingIntelligencePayloadDTO;
use App\Ai\Orchestrators\CampaignIntelligenceOrchestrator;
use App\Ai\Pipelines\AnalyticsPipeline;
use App\Ai\Pipelines\ImageGenerationPipeline;
use App\Ai\Pipelines\PromptGenerationPipeline;
use App\Models\Generation;
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
        CampaignIntelligenceOrchestrator $campaignIntelligence,
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
            | SPLIT: Image Generation Payload (visual fields only)
            |--------------------------------------------------------------------------
            */

            $imagePayload = ImageGenerationPayloadDTO::fromGeneration($generation);

            /*
            |--------------------------------------------------------------------------
            | MARKETING INTELLIGENCE (diproses terpisah, tidak masuk prompt image)
            |--------------------------------------------------------------------------
            */

            $marketingPayload = MarketingIntelligencePayloadDTO::fromGeneration($generation);
            $marketingIntelligence = null;

            try {
                $marketingIntelligence = $campaignIntelligence->buildFromMarketing($marketingPayload);
                $marketingIntelligence = $marketingIntelligence->toArray();
            } catch (\Throwable $e) {
                Log::warning('Marketing intelligence build failed (non-blocking)', [
                    'generation_id' => $this->generationId,
                    'error' => $e->getMessage(),
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | GENERATE ENHANCED PROMPT (image-only, tanpa marketing fields)
            |--------------------------------------------------------------------------
            */

            $result =
                $promptPipeline->handleForImage(
                    $imagePayload
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
                        ?? $imagePayload->negativePrompt,

                    aspectRatio: $imagePayload->aspectRatio,

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

            $aiMetadata = array_merge(
                $generation->ai_metadata ?? [],
                [
                    'replicate_prediction' => $imageResult,
                    'visual_orchestration_lifecycle' => $result['orchestration'],
                ]
            );

            // Simpan marketing intelligence ke ai_metadata (jika berhasil diproses)
            if ($marketingIntelligence !== null) {
                $aiMetadata['marketing_intelligence'] = $marketingIntelligence;
            }

            // ── Extract food_enrichment to top-level ai_metadata for direct SQL querying ──
            $foodEnrichment = data_get($result, 'orchestration.visual_intelligence.metadata.food_enrichment');
            if ($foodEnrichment !== null) {
                $aiMetadata['food_enrichment'] = $foodEnrichment instanceof \App\Ai\DTOs\FoodEnrichmentDTO
                    ? $foodEnrichment->toArray()
                    : (is_array($foodEnrichment) ? $foodEnrichment : null);
            }

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

                'ai_metadata' => $aiMetadata,

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
