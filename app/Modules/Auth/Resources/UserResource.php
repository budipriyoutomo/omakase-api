<?php

declare(strict_types=1);

namespace App\Modules\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'    => (string) $this->id,
            'email' => $this->email,
            'name'  => $this->name,
            'avatar' => $this->avatar
                ? url('storage/' . $this->avatar)
                : null,
        ];
    }
}
