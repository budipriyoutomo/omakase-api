<?php

declare(strict_types=1);

namespace App\Modules\Auth\DTOs;

final readonly class RegisterDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public ?string $name = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            name: $data['name'] ?? null,
        );
    }
}
