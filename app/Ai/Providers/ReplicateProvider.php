<?php

namespace App\Ai\Providers;

use App\Ai\Contracts\ImagePredictionProvider;
use App\Ai\Contracts\ImageProvider;
use App\Ai\DTOs\ImageGenerationDTO;
use Illuminate\Support\Facades\Http;

class ReplicateProvider implements ImagePredictionProvider, ImageProvider
{
    public function generate(
        ImageGenerationDTO $dto
    ): array {

        $response = Http::withToken(
            config(
                'services.replicate.api_key'
            )
        )
            ->post(
                'https://api.replicate.com/v1/models/black-forest-labs/flux-schnell/predictions',
                [

                    'input' => [

                        'prompt' => $dto->prompt,

                        'negative_prompt' => $dto->negativePrompt,

                        'aspect_ratio' => $dto->aspectRatio ?: '1:1',

                        'num_outputs' => $dto->numImages,
                        'output_format' => 'jpg',

                        'output_quality' => 80,
                    ],
                ]
            );

        return $response->json();
    }

    public function prediction(string $predictionId): array
    {
        $response = Http::withToken(
            config('services.replicate.api_key')
        )->get(
            "https://api.replicate.com/v1/predictions/{$predictionId}"
        );

        return [
            'successful' => $response->successful(),
            'payload' => $response->json(),
        ];
    }
}
