<?php

declare(strict_types=1);

namespace App\Modules\Generation\Services;

use App\Shared\Contracts\AiServiceInterface;
use App\Shared\Exceptions\AiException;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Facades\Ai;

class GeminiAiService implements AiServiceInterface
{
    private string $model;

    public function __construct()
    {
        $this->model = config('GEMINI_MODEL', 'gemini-2.0-flash-exp');
    }

    /**
     * Generate marketing content based on a structured prompt using Google Gemini.
     *
     * @throws AiException
     */
    public function generateContent(string $prompt, array $options = []): string
    {
        try {
            $response = Ai::provider('gemini')
                ->using($this->model)
                ->withSystemPrompt($this->buildSystemPrompt())
                ->prompt($prompt)
                ->generate();

            return $response->text();
        } catch (\Throwable $e) {
            Log::error('Gemini AI generation failed', [
                'error' => $e->getMessage(),
                'prompt' => substr($prompt, 0, 200),
            ]);

            throw new AiException('Failed to generate content. Please try again.', $e);
        }
    }

    /**
     * Generate a list of AI suggestions for the dashboard.
     *
     * @return string[]
     *
     * @throws AiException
     */
    public function generateSuggestions(string $context, int $count = 5): array
    {
        $prompt = "Generate {$count} short, actionable marketing campaign ideas for: {$context}. Return as a numbered list, one idea per line.";

        try {
            $response = Ai::provider('gemini')
                ->using($this->model)
                ->prompt($prompt)
                ->generate();

            $lines = array_filter(
                array_map('trim', explode("\n", $response->text())),
                fn (string $line) => ! empty($line),
            );

            return array_values(array_slice($lines, 0, $count));
        } catch (\Throwable $e) {
            Log::error('Gemini suggestions failed', ['error' => $e->getMessage()]);
            throw new AiException('Failed to generate suggestions.', $e);
        }
    }

    /**
     * Build a marketing-focused system prompt for Gemini.
     */
    private function buildSystemPrompt(): string
    {
        return <<<'PROMPT'
        You are an expert marketing copywriter and campaign strategist.
        Your task is to generate high-quality, conversion-focused marketing content.
        Always be concise, persuasive, and tailored to the specified platform and audience.
        Format your output clearly with sections when appropriate.
        PROMPT;
    }
}
