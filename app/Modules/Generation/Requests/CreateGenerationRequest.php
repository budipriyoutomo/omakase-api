<?php

declare(strict_types=1);

namespace App\Modules\Generation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGenerationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'campaignType' => ['required', 'string', 'max:100'],
            'platform' => ['required', 'string', 'max:100'],
            'style' => ['required', 'string', 'max:100'],
            'prompt' => ['required', 'string', 'min:10', 'max:2000'],
            'cuisine' => ['nullable', 'string', 'max:100'],
            'audience' => ['nullable', 'string', 'max:100'],
            'goal' => ['nullable', 'string', 'max:100'],
            'mood' => ['nullable', 'string', 'max:100'],
            'heroItem' => ['nullable', 'string', 'max:160'],
            'visualStrategy' => ['nullable', 'string', 'max:160'],
            'ctaStrategy' => ['nullable', 'string', 'max:160'],
            'aspectRatio' => ['nullable', 'string', 'max:20'],
            'negativePrompt' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
