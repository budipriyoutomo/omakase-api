<?php

namespace App\Ai\Exceptions;

use RuntimeException;

final class InvalidAiResponseException extends RuntimeException
{
    public static function invalidJson(string $reason): self
    {
        return new self("AI returned invalid JSON: {$reason}.");
    }

    public static function unexpectedType(string $expected, string $actual): self
    {
        return new self("AI response type mismatch. Expected [{$expected}], got [{$actual}].");
    }

    public static function emptyResponse(): self
    {
        return new self("AI returned an empty response.");
    }
}