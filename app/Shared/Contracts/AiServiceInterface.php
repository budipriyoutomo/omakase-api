<?php

declare(strict_types=1);

namespace App\Shared\Contracts;

interface AiServiceInterface
{
    /**
     * Generate marketing content based on a structured prompt.
     */
    public function generateContent(string $prompt, array $options = []): string;

    /**
     * Generate AI suggestions (short list of ideas).
     *
     * @return string[]
     */
    public function generateSuggestions(string $context, int $count = 5): array;
}
