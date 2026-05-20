<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

class AiException extends ApiException
{
    public function __construct(string $message = 'AI generation failed.', ?\Throwable $previous = null)
    {
        parent::__construct($message, 503, [], $previous);
    }
}
