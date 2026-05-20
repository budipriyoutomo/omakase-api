<?php

declare(strict_types=1);

namespace App\Modules\Template\Controllers;

use App\Modules\Template\Resources\TemplateResource;
use App\Shared\Traits\ApiResponseTrait;
use App\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class TemplateController extends Controller
{
    use ApiResponseTrait;

    // GET /v1/templates
    public function index(): JsonResponse
    {
        $templates = Template::orderBy('name')->get();

        return $this->success(TemplateResource::collection($templates));
    }

    // GET /v1/templates/{id}
    public function show(string $id): JsonResponse
    {
        $template = Template::findOrFail($id);

        return $this->success(new TemplateResource($template));
    }
}
