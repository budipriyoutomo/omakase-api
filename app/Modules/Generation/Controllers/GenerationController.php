<?php

declare(strict_types=1);

namespace App\Modules\Generation\Controllers;

use App\Modules\Generation\DTOs\CreateGenerationDTO;
use App\Modules\Generation\Requests\CreateGenerationRequest;
use App\Modules\Generation\Resources\GenerationResource;
use App\Modules\Generation\Services\GenerationService;
use App\Shared\DTOs\PaginationDTO;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class GenerationController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly GenerationService $service) {}

    // POST /v1/generations
    public function store(CreateGenerationRequest $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $dto = CreateGenerationDTO::fromArray($user->id, $request->validated());
        $generation = $this->service->create($dto);

        return $this->created(new GenerationResource($generation));
    }

    // GET /v1/generations
    public function index(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $pagination = PaginationDTO::fromRequest($request);
        $filters = $request->only(['platform', 'q']);

        $paginated = $this->service->listForUser($user->id, $filters, $pagination->perPage);

        return $this->success([
            'items' => GenerationResource::collection($paginated->items()),
            'meta' => [
                'total' => $paginated->total(),
                'page' => $paginated->currentPage(),
                'pageSize' => $paginated->perPage(),
            ],
        ]);
    }

    // GET /v1/generations/{id}
    public function show(string $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $generation = $this->service->getForUser($id, $user->id);

        return $this->success(new GenerationResource($generation));
    }

    // DELETE /v1/generations/{id}
    public function destroy(string $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $this->service->deleteForUser($id, $user->id);

        return $this->noContent();
    }

    // POST /v1/generations/{id}/duplicate
    public function duplicate(string $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $generation = $this->service->duplicate($id, $user->id);

        return $this->created(new GenerationResource($generation));
    }

    // POST /v1/generations/{id}/regenerate
    public function regenerate(string $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $generation = $this->service->regenerate($id, $user->id);

        return $this->success(new GenerationResource($generation));
    }

        // GET /v1/generations/{id}/creative
    public function creative(string $id): Response | JsonResponse
    {
        $user       = JWTAuth::parseToken()->authenticate();
        $generation = $this->service->getForUser($id, $user->id);
         
        $html = data_get($generation->ai_metadata, 'creative_html');
        if (! $html) {
                    return response()->json([
            'message' => 'Creative HTML is not ready yet.',
        ], 404);
        }

        return response($html, 200)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('X-Creative-Theme',  data_get($generation->ai_metadata, 'creative_blueprint.theme', ''))
            ->header('X-Creative-Layout', data_get($generation->ai_metadata, 'creative_blueprint.layout_mode', ''));
    }

    /**
     * POST /v1/generations/{id}/creative/render
     * Re-render creative HTML dengan custom typography (dari preview editor).
     */
    public function renderCreative(Request $request, string $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $generation = $this->service->getForUser($id, $user->id);

        $creativeBlueprint = data_get($generation->ai_metadata, 'creative_blueprint');
        if (!$creativeBlueprint) {
            return $this->error('Creative blueprint not available yet.', 404);
        }

        $imageUrl = $generation->image_url;
        if (!$imageUrl) {
            return $this->error('Image not available yet.', 404);
        }

        try {
            // Merge custom typography overrides into creative blueprint
            $customTypography = $request->input('typography', []);

            // Update component content & styles based on custom typography
            if (!empty($customTypography)) {
                $components = $creativeBlueprint['components'] ?? [];
                foreach ($components as &$component) {
                    $type = $component['type'] ?? '';

                    if ($type === 'hero_headline' && isset($customTypography['headline'])) {
                        $h = $customTypography['headline'];
                        if (isset($h['text'])) {
                            $component['content'] = $h['text'];
                        }
                        if (isset($h['font_size'])) {
                            $component['styles']['font-size'] = $h['font_size'];
                        }
                        if (isset($h['font_family'])) {
                            $component['styles']['font-family'] = $h['font_family'];
                        }
                        if (isset($h['position'])) {
                            $component['position'] = $h['position'];
                        }
                    }

                    if ($type === 'sub_headline' && isset($customTypography['subheadline'])) {
                        $s = $customTypography['subheadline'];
                        if (isset($s['text'])) {
                            $component['content'] = $s['text'];
                        }
                        if (isset($s['font_size'])) {
                            $component['styles']['font-size'] = $s['font_size'];
                        }
                        if (isset($s['position'])) {
                            $component['position'] = $s['position'];
                        }
                    }

                    if ($type === 'luxury_cta' && isset($customTypography['cta'])) {
                        $c = $customTypography['cta'];
                        if (isset($c['text'])) {
                            $component['content'] = $c['text'];
                        }
                        if (isset($c['font_size'])) {
                            $component['styles']['font-size'] = $c['font_size'];
                        }
                        if (isset($c['position'])) {
                            $component['position'] = $c['position'];
                        }
                    }
                }
                unset($component);
                $creativeBlueprint['components'] = $components;

                // Update tokens typography if custom fonts specified
                if (isset($customTypography['font_pairing'])) {
                    $fp = $customTypography['font_pairing'];
                    if (isset($fp['headline'])) {
                        $creativeBlueprint['tokens']['typography']['headline_font'] = $fp['headline'];
                    }
                    if (isset($fp['body'])) {
                        $creativeBlueprint['tokens']['typography']['body_font'] = $fp['body'];
                    }
                }
            }

            // Re-render HTML using CreativeBlueprintAssembler & HtmlCreativeRenderer
            $blueprintDTO = new \App\Ai\Creative\Blueprints\CreativeBlueprintDTO(
                theme: $creativeBlueprint['theme'] ?? '',
                layoutMode: $creativeBlueprint['layout_mode'] ?? '',
                canvas: $creativeBlueprint['canvas'] ?? [],
                tokens: $creativeBlueprint['tokens'] ?? [],
                components: $creativeBlueprint['components'] ?? [],
                overlayStrategy: $creativeBlueprint['overlay_strategy'] ?? [],
            );

            $renderer = app(\App\Ai\Renderers\Html\HtmlCreativeRenderer::class);
            $html = $renderer->render($blueprintDTO, $imageUrl);

            // ── Persist ALL edits back to database so they survive page refresh ──
            $aiMetadata = $generation->ai_metadata ?? [];
            $aiMetadata['creative_blueprint'] = $creativeBlueprint;
            $aiMetadata['creative_html'] = $html;

            // Also persist typography_blueprint edits so Typography tab reflects edits
            if (!empty($customTypography)) {
                $typographyBlueprint = $aiMetadata['typography_blueprint'] ?? [];

                if (isset($customTypography['headline'])) {
                    $h = $customTypography['headline'];
                    if (isset($h['text']))       $typographyBlueprint['headline']['text']       = $h['text'];
                    if (isset($h['font_size']))  $typographyBlueprint['headline']['font_size']  = $h['font_size'];
                    if (isset($h['font_family'])) $typographyBlueprint['headline']['font_family'] = $h['font_family'];
                    if (isset($h['position']))    $typographyBlueprint['headline']['safe_area']   = [
                        'x' => $h['position']['x'] ?? 300,
                        'y' => $h['position']['y'] ?? 300,
                    ];
                }

                if (isset($customTypography['subheadline'])) {
                    $s = $customTypography['subheadline'];
                    if (isset($s['text']))       $typographyBlueprint['subheadline']['text']       = $s['text'];
                    if (isset($s['font_size']))  $typographyBlueprint['subheadline']['font_size']  = $s['font_size'];
                }

                if (isset($customTypography['cta'])) {
                    $c = $customTypography['cta'];
                    if (isset($c['text']))       $typographyBlueprint['cta']['text']       = $c['text'];
                    if (isset($c['font_size']))  $typographyBlueprint['cta']['font_size']  = $c['font_size'];
                }

                if (isset($customTypography['font_pairing'])) {
                    $fp = $customTypography['font_pairing'];
                    $typographyBlueprint['font_pairing'] = array_merge(
                        $typographyBlueprint['font_pairing'] ?? [],
                        array_filter([
                            'headline' => $fp['headline'] ?? null,
                            'body'     => $fp['body']     ?? null,
                        ], fn($v) => $v !== null),
                    );
                }

                $aiMetadata['typography_blueprint'] = $typographyBlueprint;
            }

            $generation->update(['ai_metadata' => $aiMetadata]);

            return $this->success([
                'html' => $html,
                'creative_blueprint' => $creativeBlueprint,
            ]);
        } catch (\Throwable $e) {
            Log::error('renderCreative failed', [
                'generation_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->error('Failed to render creative: ' . $e->getMessage(), 500);
        }
    }
}