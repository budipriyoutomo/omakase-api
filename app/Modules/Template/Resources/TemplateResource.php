<?php

declare(strict_types=1);

namespace App\Modules\Template\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Template
 */
class TemplateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'category' => $this->category,
            'style'    => $this->style,
            'payload'  => $this->payload ?? [],
        ];
    }
}