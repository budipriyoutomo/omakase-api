<?php

declare(strict_types=1);

namespace App\Modules\BrandKit\Controllers;

use App\Modules\BrandKit\Resources\BrandKitResource;
use App\Modules\BrandKit\Services\BrandKitService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class BrandKitController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly BrandKitService $service) {}

    // GET /v1/brand-kit
    public function show(): JsonResponse
    {
        $user     = JWTAuth::parseToken()->authenticate();
        $brandKit = $this->service->getOrCreate($user->id);

        return $this->success(new BrandKitResource($brandKit));
    }

    // PATCH /v1/brand-kit
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'primaryColor' => ['nullable', 'string', 'max:10'],
            'accentColor'  => ['nullable', 'string', 'max:10'],
            'fontFamily'   => ['nullable', 'string', 'max:100'],
        ]);

        $user     = JWTAuth::parseToken()->authenticate();
        $brandKit = $this->service->upsert($user->id, $request->only([
            'primaryColor', 'accentColor', 'fontFamily',
        ]));

        return $this->success(new BrandKitResource($brandKit));
    }

    // POST /v1/brand-kit/logo
    public function uploadLogo(Request $request): JsonResponse
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:jpeg,png,webp,svg', 'max:5120'],
        ]);

        $user     = JWTAuth::parseToken()->authenticate();
        $brandKit = $this->service->uploadLogo($user->id, $request->file('logo'));

        return $this->success(new BrandKitResource($brandKit));
    }
}
