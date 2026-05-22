<?php

namespace App\Ai\Typography\Vision;

use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\Exceptions\ImageFetchException;
use App\Ai\Exceptions\InvalidImageUrlException;
use App\Ai\Exceptions\InvalidAiResponseException;
use App\Ai\Typography\DTOs\TypographyBlueprintDTO;
use Illuminate\Support\Facades\Http;
use Laravel\Ai\Files;

final class TypographyVisionAnalyzer
{
    public function analyze(string $imageUrl, CampaignPayloadDTO $dto): TypographyBlueprintDTO
    {
        $this->validateImageUrl($imageUrl);

        // Download image ke temp file
        $imageData = $this->fetchImageData($imageUrl);
        $tempPath  = tempnam(sys_get_temp_dir(), 'campaign_') . '.jpg';
        file_put_contents($tempPath, $imageData);

        try {
            $response = (new TypographyVisionAgent($dto))
                ->prompt(
                    'Analyze this campaign image and return the typography layout blueprint.',
                    attachments: [
                        Files\Image::fromPath($tempPath),
                    ]
                ); 

            return TypographyBlueprintDTO::fromArray(
                $this->extractJson($response->text)
            );
        } finally {
            @unlink($tempPath);
        }
    }

    private function fetchImageData(string $imageUrl): string
    {
        $response = Http::timeout(10)->get($imageUrl);

        if (! $response->successful()) {
            throw ImageFetchException::fromUrl($imageUrl, $response->status());
        }

        return $response->body();
    }

    private function validateImageUrl(string $url): void
    {
        $parsed = parse_url($url);

        if (($parsed['scheme'] ?? '') !== 'https') {
            throw InvalidImageUrlException::schemeNotAllowed($url);
        }
    }

    private function extractJson(string $text): array
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
        $text = preg_replace('/\s*```$/i', '', $text);

        $decoded = json_decode(trim($text), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw InvalidAiResponseException::invalidJson('Invalid JSON from Gemini: ' . json_last_error_msg());
        }

        if (! is_array($decoded)) {
            throw InvalidAiResponseException::unexpectedType('array', gettype($decoded));
        }

        return $decoded;
    }
}