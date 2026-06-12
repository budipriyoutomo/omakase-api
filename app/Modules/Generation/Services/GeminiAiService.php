<?php

declare(strict_types=1);

namespace App\Modules\Generation\Services;

use App\Modules\Generation\Agents\ContentGenerationAgent;
use App\Modules\Generation\Agents\SuggestionsAgent;
use App\Shared\Contracts\AiServiceInterface;
use App\Shared\Exceptions\AiException;
use Illuminate\Support\Facades\Log;

class GeminiAiService implements AiServiceInterface
{
    public function generateContent(string $prompt, array $options = []): string
    {
        try {
            $response = (new ContentGenerationAgent)->prompt($prompt);

            return $response->text;

        } catch (\Throwable $e) {
            Log::error('Gemini AI generation failed', [
                'error'  => $e->getMessage(),
                'prompt' => substr($prompt, 0, 200),
            ]);

            throw new AiException('Failed to generate content. Please try again.', $e);
        }
    }

    public function generateSuggestions(string $context, int $count = 5): array
    {
        $prompt = "Generate {$count} short, actionable marketing campaign ideas for: {$context}. Return as a numbered list, one idea per line.";

        try {
            $response = (new SuggestionsAgent)->prompt($prompt);

            $lines = array_filter(
                array_map('trim', explode("\n", $response->text)),
                fn (string $line) => ! empty($line),
            );

            return array_values(array_slice($lines, 0, $count));

        } catch (\Throwable $e) {
            Log::error('Gemini suggestions failed', [
                'error' => $e->getMessage(),
            ]);

            throw new AiException('Failed to generate suggestions.', $e);
        }
    }
}