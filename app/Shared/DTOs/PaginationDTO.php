<?php

declare(strict_types=1);

namespace App\Shared\DTOs;

final readonly class PaginationDTO
{
    public function __construct(
        public int $page = 1,
        public int $perPage = 15,
        public string $sortBy = 'created_at',
        public string $sortDir = 'desc',
    ) {}

    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            page: max(1, (int) $request->input('page', 1)),
            perPage: min(100, max(1, (int) $request->input('limit', 15))),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDir: in_array($request->input('sort_dir', 'desc'), ['asc', 'desc']) ? $request->input('sort_dir', 'desc') : 'desc',
        );
    }
}
