<?php

namespace App\Ai\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImageStorageService
{
    public function storeFromUrl(
        string $url,
        string $path = 'generations'
    ): string {

        /*
        |--------------------------------------------------------------------------
        | DOWNLOAD IMAGE
        |--------------------------------------------------------------------------
        */

        $image =
            Http::get($url)->body();

        /*
        |--------------------------------------------------------------------------
        | GENERATE FILE NAME
        |--------------------------------------------------------------------------
        */

        $filename =
            $path.'/'.
            now()->format('Y/m/d/').
            uniqid().'.jpg';

        /*
        |--------------------------------------------------------------------------
        | STORE TO S3
        |--------------------------------------------------------------------------
        */

        Storage::disk('s3')->put(
            $filename,
            $image,
            'public'
        );

        /*
        |--------------------------------------------------------------------------
        | RETURN URL
        |--------------------------------------------------------------------------
        */

        return Storage::disk('s3')
            ->url($filename);
    }
}
