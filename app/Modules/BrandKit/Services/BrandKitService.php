<?php

declare(strict_types=1);

namespace App\Modules\BrandKit\Services;

use App\Models\BrandKit;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BrandKitService
{
    public function getOrCreate(int $userId): BrandKit
    {
        return BrandKit::firstOrCreate(['user_id' => $userId]);
    }

    public function upsert(int $userId, array $data): BrandKit
    {
        $mapped = [];
        if (array_key_exists('primaryColor', $data)) $mapped['primary_color'] = $data['primaryColor'];
        if (array_key_exists('accentColor', $data))  $mapped['accent_color']  = $data['accentColor'];
        if (array_key_exists('fontFamily', $data))   $mapped['font_family']   = $data['fontFamily'];

        $brandKit = BrandKit::updateOrCreate(['user_id' => $userId], $mapped);
        return $brandKit;
    }

    public function uploadLogo(int $userId, UploadedFile $file): BrandKit
    {
        $brandKit = $this->getOrCreate($userId);

        if ($brandKit->logo_url) {
            // Extract path from URL and delete old file
            $oldPath = str_replace(url('storage') . '/', '', $brandKit->logo_url);
            Storage::disk('public')->delete($oldPath);
        }

        $path = $file->store('logos', 'public');
        $brandKit->update(['logo_url' => url('storage/' . $path)]);

        return $brandKit->fresh();
    }
}
