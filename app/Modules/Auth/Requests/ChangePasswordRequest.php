<?php

declare(strict_types=1);

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'currentPassword' => ['required', 'string'],
            'newPassword'     => ['required', 'string', 'min:8', 'different:currentPassword'],
        ];
    }
}
