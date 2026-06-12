<?php

declare(strict_types=1);

namespace App\Modules\Generation\Jobs;

use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\Typography\Vision\TypographyVisionAnalyzer;
use App\Ai\Creative\CreativeBlueprintAssembler;
use App\Ai\Renderers\Html\HtmlCreativeRenderer;
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
        TypographyVisionAnalyzer $analyzer,
        CreativeBlueprintAssembler $assembler,
        HtmlCreativeRenderer $renderer,
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

        $typographyBlueprint = $analyzer->analyze(
            $imageUrl,
            $dto
        );

        /*
        |--------------------------------------------------------------------------
        | ASSEMBLE CREATIVE BLUEPRINT
        |--------------------------------------------------------------------------
        */

        $creativeBlueprint = $assembler->assemble(
            campaign:   $dto,
            typography: $typographyBlueprint,
        );

        /*
        |--------------------------------------------------------------------------
        | RENDER HTML
        |--------------------------------------------------------------------------
        */

        $html = $renderer->render(
            blueprint: $creativeBlueprint,
            imageUrl:  $imageUrl,
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
                    'typography_blueprint' => $typographyBlueprint->toArray(),
                    'creative_blueprint'   => $creativeBlueprint->toArray(),
                    'creative_html'       => $html,
                ]
            ),
        ]);
 
        // ✅ Update pesannya
        Log::info('AnalyzeTypographyJob: blueprints assembled and saved', [
            'generation_id' => $generation->id,
            'theme'         => $creativeBlueprint->theme,
            'components'    => count($creativeBlueprint->components),
        ]);

    }
    
    public function failed(Throwable $exception): void
    {
        Log::error('AnalyzeTypographyJob permanently failed', [
            'generation_id' => $this->generationId,
            'error'         => $exception->getMessage(),
        ]);

        $generation = Generation::find($this->generationId);

        if (! $generation) {
            return;
        }

        $generation->update([
            'ai_metadata' => array_merge(
                $generation->ai_metadata ?? [],
                [
                    'blueprint_error' => [
                        'message'   => $exception->getMessage(),
                        'failed_at' => now()->toIso8601String(),
                    ],
                ]
            ),
        ]);
    }
}