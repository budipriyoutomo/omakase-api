<?php

declare(strict_types=1);

namespace App\Shared\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function findById(string|int $id): ?Model;

    public function findByIdOrFail(string|int $id): Model;

    public function all(): iterable;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function create(array $data): Model;

    public function update(string|int $id, array $data): Model;

    public function delete(string|int $id): bool;
}
