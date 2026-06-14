<?php

declare(strict_types=1);

namespace App\Modules\Social\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateScheduledPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'campaign_generation_id' => ['nullable', 'exists:generations,id'],
            'social_account_id' => ['required', 'exists:social_accounts,id'],
            'image_url' => ['required', 'url', 'max:2048'],
            'caption' => ['required', 'string', 'max:2200'],
            'hashtags' => ['nullable', 'array', 'max:30'],
            'hashtags.*' => ['string', 'max:100'],
            'scheduled_at' => ['required', 'date', 'after:now +10 minutes'],
        ];
    }

    public function messages(): array
    {
        return [
            'scheduled_at.after' => 'Scheduled time must be at least 10 minutes from now.',
            'hashtags.max' => 'You can add a maximum of 30 hashtags.',
            'caption.max' => 'Caption cannot exceed 2,200 characters.',
        ];
    }
}