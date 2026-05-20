<?php

declare(strict_types=1);

namespace App\Modules\BrandKit\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\BrandKit
 */
class BrandKitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'logoUrl'      => $this->logo_url,
            'primaryColor' => $this->primary_color,
            'accentColor'  => $this->accent_color,
            'fontFamily'   => $this->font_family,
        ];
    }
}
