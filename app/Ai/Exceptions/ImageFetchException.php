<?php

namespace App\Ai\Exceptions;

use RuntimeException;

final class ImageFetchException extends RuntimeException
{
    public static function fromUrl(string $url, int $statusCode): self
    {
        return new self("Failed to fetch image from [{$url}]. HTTP status: {$statusCode}.");
    }

    public static function unreachable(string $url): self
    {
        return new self("Image URL [{$url}] is unreachable or timed out.");
    }
}