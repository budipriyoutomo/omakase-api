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
}
