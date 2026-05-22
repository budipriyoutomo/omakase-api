<?php

namespace App\Ai\Exceptions;

use InvalidArgumentException;

final class InvalidImageUrlException extends InvalidArgumentException
{
    public static function schemeNotAllowed(string $url): self
    {
        return new self("Image URL [{$url}] is not allowed. Only HTTPS URLs are accepted.");
    }

    public static function privateAddress(string $url): self
    {
        return new self("Image URL [{$url}] resolves to a private or reserved IP address.");
    }
}