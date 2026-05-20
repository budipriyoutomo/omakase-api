<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use Exception;
use Throwable;

class ApiException extends Exception
{
    public function __construct(
        string $message = 'An error occurred.',
        private readonly int $statusCode = 400,
        private readonly array $errors = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function render(): \Illuminate\Http\JsonResponse
    {
        $body = [
            'message' => $this->getMessage(),
            'status'  => $this->statusCode,
        ];

        if (!empty($this->errors)) {
            $body['errors'] = $this->errors;
        }

        return response()->json($body, $this->statusCode);
    }
}
