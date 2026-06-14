<?php

declare(strict_types=1);

namespace App\Modules\Social\Controllers;

use App\Models\ScheduledPost;
use App\Models\SocialAccount;
use App\Modules\Social\Requests\CreateScheduledPostRequest;
use App\Modules\Social\Resources\ScheduledPostResource;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ScheduledPostController extends Controller
{
    use ApiResponseTrait;

    /**
     * GET /v1/scheduled-posts
     * List scheduled posts, filterable by status.
     */
    public function index(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        $query = ScheduledPost::with('socialAccount')
            ->where('user_id', $user->id)
            ->latest('scheduled_at');

        $status = $request->query('status');
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $posts = $query->paginate(20);

        return $this->success([
            'items' => ScheduledPostResource::collection($posts->items()),
            'meta' => [
                'total' => $posts->total(),
                'page' => $posts->currentPage(),
                'pageSize' => $posts->perPage(),
            ],
        ]);
    }

    /**
     * GET /v1/scheduled-posts/{id}
     * Show a single scheduled post.
     */
    public function show(string $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        $post = ScheduledPost::with('socialAccount')
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return $this->success(new ScheduledPostResource($post));
    }

    /**
     * POST /v1/scheduled-posts
     * Create a new scheduled post.
     */
    public function store(CreateScheduledPostRequest $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Verify social account belongs to the user
        $socialAccount = SocialAccount::where('user_id', $user->id)
            ->findOrFail($request->input('social_account_id'));

        $post = ScheduledPost::create([
            'user_id' => $user->id,
            'social_account_id' => $socialAccount->id,
            'generation_id' => $request->input('campaign_generation_id'),
            'image_url' => $request->input('image_url'),
            'caption' => $request->input('caption'),
            'hashtags' => $request->input('hashtags'),
            'scheduled_at' => $request->input('scheduled_at'),
            'status' => ScheduledPost::STATUS_PENDING,
        ]);

        return $this->created(new ScheduledPostResource($post->load('socialAccount')));
    }

    /**
     * PATCH /v1/scheduled-posts/{id}
     * Update caption/time — only if status is 'pending'.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        $post = ScheduledPost::where('user_id', $user->id)->findOrFail($id);

        if (!$post->isPending()) {
            return $this->error('Only pending posts can be updated.', 422);
        }

        $validated = $request->validate([
            'caption' => ['sometimes', 'string', 'max:2200'],
            'hashtags' => ['nullable', 'array', 'max:30'],
            'hashtags.*' => ['string', 'max:100'],
            'scheduled_at' => ['sometimes', 'date', 'after:now +10 minutes'],
        ]);

        $post->update($validated);

        return $this->success(new ScheduledPostResource($post->load('socialAccount')), 'Post updated.');
    }

    /**
     * DELETE /v1/scheduled-posts/{id}
     * Cancel a scheduled post (set status to cancelled).
     */
    public function destroy(string $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        $post = ScheduledPost::where('user_id', $user->id)->findOrFail($id);

        if ($post->isPublished()) {
            return $this->error('Cannot cancel an already published post.', 422);
        }

        $post->update(['status' => ScheduledPost::STATUS_CANCELLED]);

        return $this->success(new ScheduledPostResource($post->load('socialAccount')), 'Post cancelled.');
    }
}