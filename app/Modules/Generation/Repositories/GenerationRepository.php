<?php

declare(strict_types=1);

namespace App\Modules\Generation\Repositories;

use App\Models\Generation;
use App\Shared\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GenerationRepository implements RepositoryInterface
{
    public function findById(string|int $id): ?Generation
    {
        return Generation::find($id);
    }

    public function findByIdOrFail(string|int $id): Generation
    {
        return Generation::findOrFail($id);
    }

    public function all(): iterable
    {
        return Generation::all();
    }

    /**
     * Paginate generations for a specific user with optional filters.
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Generation::query();

        if (isset($filters['user_id'])) {
            $query->forUser($filters['user_id']);
        }

        if (! empty($filters['platform'])) {
            $query->where('platform', $filters['platform']);
        }

        if (! empty($filters['q'])) {
            $query->where('prompt', 'ilike', '%'.$filters['q'].'%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): Generation
    {
        return Generation::create($data);
    }

    public function update(string|int $id, array $data): Generation
    {
        $generation = $this->findByIdOrFail($id);
        $generation->update($data);

        return $generation->fresh();
    }

    public function delete(string|int $id): bool
    {
        return Generation::destroy($id) > 0;
    }

    public function findForUser(string $id, int $userId): ?Generation
    {
        return Generation::where('id', $id)->where('user_id', $userId)->first();
    }
}
