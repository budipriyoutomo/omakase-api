<?php

declare(strict_types=1);

namespace App\Modules\Generation\Services;

use App\Models\Generation;
use App\Modules\Generation\DTOs\CreateGenerationDTO;
use App\Modules\Generation\Events\GenerationCreated;
use App\Modules\Generation\Jobs\ProcessGenerationJob;
use App\Modules\Generation\Repositories\GenerationRepository;
use App\Shared\Exceptions\ApiException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GenerationService
{
    public function __construct(
        private readonly GenerationRepository $repository,
    ) {}

    /**
     * Create a new generation record and dispatch the AI processing job.
     */
    public function create(CreateGenerationDTO $dto): Generation
    {
        $generation = $this->repository->create([
            'user_id' => $dto->userId,
            'campaign_type' => $dto->campaignType,
            'cuisine' => $dto->cuisine,
            'platform' => $dto->platform,
            'audience' => $dto->audience,
            'goal' => $dto->goal,
            'mood' => $dto->mood,
            'style' => $dto->style,
            'hero_item' => $dto->heroItem,
            'visual_strategy' => $dto->visualStrategy,
            'cta_strategy' => $dto->ctaStrategy,
            'aspect_ratio' => $dto->aspectRatio,
            'prompt' => $dto->prompt,
            'negative_prompt' => $dto->negativePrompt,
            'status' => Generation::STATUS_PENDING,
        ]);

        GenerationCreated::dispatch($generation);
        ProcessGenerationJob::dispatch($generation->id);

        return $generation;
    }

    /**
     * Get paginated generation history for a user.
     */
    public function listForUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, array_merge(['user_id' => $userId], $filters));
    }

    /**
     * Get a single generation (user-scoped for security).
     *
     * @throws ApiException
     */
    public function getForUser(string $id, int $userId): Generation
    {
        $generation = $this->repository->findForUser($id, $userId);

        if (! $generation) {
            throw new ApiException('Generation not found.', 404);
        }

        return $generation;
    }

    /**
     * Delete a generation (user-scoped).
     *
     * @throws ApiException
     */
    public function deleteForUser(string $id, int $userId): void
    {
        $generation = $this->getForUser($id, $userId);
        $this->repository->delete($generation->id);
    }

    /**
     * Duplicate a generation as a new pending record.
     *
     * @throws ApiException
     */
    public function duplicate(string $id, int $userId): Generation
    {
        $original = $this->getForUser($id, $userId);

        return $this->create(new CreateGenerationDTO(
            userId: $userId,
            campaignType: $original->campaign_type ?? '',
            platform: $original->platform ?? '',
            style: $original->style ?? '',
            prompt: $original->prompt,
            cuisine: $original->cuisine,
            audience: $original->audience,
            goal: $original->goal,
            mood: $original->mood,
            heroItem: $original->hero_item,
            visualStrategy: $original->visual_strategy,
            ctaStrategy: $original->cta_strategy,
            aspectRatio: $original->aspect_ratio,
            negativePrompt: $original->negative_prompt,
        ));
    }

    /**
     * Re-run AI generation for an existing record.
     *
     * @throws ApiException
     */
    public function regenerate(string $id, int $userId): Generation
    {
        $generation = $this->getForUser($id, $userId);

        $generation->update(['status' => Generation::STATUS_PENDING, 'result' => null]);
        ProcessGenerationJob::dispatch($generation->id);

        return $generation->fresh();
    }
}
