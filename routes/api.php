<?php

declare(strict_types=1);

use App\Modules\Analytics\Controllers\AnalyticsController;
use App\Modules\Auth\Controllers\AuthController;
use App\Modules\BrandKit\Controllers\BrandKitController;
use App\Modules\Dashboard\Controllers\DashboardController;
use App\Modules\Generation\Controllers\GenerationController;
use App\Modules\KnowledgeBase\Controllers\CampaignContextController;
use App\Modules\KnowledgeBase\Controllers\CuisineStyleController;
use App\Modules\KnowledgeBase\Controllers\FoodCategoryController;
use App\Modules\Social\Controllers\InstagramOAuthController;
use App\Modules\Social\Controllers\ScheduledPostController;
use App\Modules\Social\Controllers\SocialAccountController;
use App\Modules\Subscription\Controllers\SubscriptionController;
use App\Modules\Template\Controllers\TemplateController;
use App\Modules\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Omakase AI — API Routes (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->name('v1.')->group(function () {

    // ── Public Auth Routes ──────────────────────────────────────────
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('login',           [AuthController::class, 'login'])->name('login');
        Route::post('register',        [AuthController::class, 'register'])->name('register');
        Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
        Route::post('refresh',         [AuthController::class, 'refresh'])->name('refresh');
    });

    // ── Subscription Plans (public) ─────────────────────────────────
    Route::get('subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');

    // ── Protected Routes (JWT required) ────────────────────────────
    Route::middleware('jwt')->group(function () {

        // Auth
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('logout',          [AuthController::class, 'logout'])->name('logout');
            Route::get('me',               [AuthController::class, 'me'])->name('me');
            Route::post('change-password', [AuthController::class, 'changePassword'])->name('change-password');
            Route::post('me/avatar',       [AuthController::class, 'uploadAvatar'])->name('avatar');
        });

        // Dashboard
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('stats',               [DashboardController::class, 'stats'])->name('stats');
            Route::get('usage',               [DashboardController::class, 'usage'])->name('usage');
            Route::get('suggestions',         [DashboardController::class, 'suggestions'])->name('suggestions');
            Route::get('templates/trending',  [DashboardController::class, 'trendingTemplates'])->name('templates.trending');
        });

        // AI Generations
        Route::prefix('generations')->name('generations.')->group(function () {
            Route::get('/',         [GenerationController::class, 'index'])->name('index');
            Route::post('/',        [GenerationController::class, 'store'])->name('store');
            Route::get('{id}',      [GenerationController::class, 'show'])->name('show');
            Route::delete('{id}',   [GenerationController::class, 'destroy'])->name('destroy');
            Route::post('{id}/duplicate',   [GenerationController::class, 'duplicate'])->name('duplicate');
            Route::post('{id}/regenerate',  [GenerationController::class, 'regenerate'])->name('regenerate');

            Route::post('{id}/creative', [GenerationController::class, 'creative'])->name('creative');

            // Render ulang creative HTML dengan custom typography (edit preview)
            Route::post('{id}/creative/render', [GenerationController::class, 'renderCreative'])->name('creative.render');
        });

        // Brand Kit
        Route::prefix('brand-kit')->name('brand-kit.')->group(function () {
            Route::get('/',    [BrandKitController::class, 'show'])->name('show');
            Route::patch('/',  [BrandKitController::class, 'update'])->name('update');
            Route::post('logo', [BrandKitController::class, 'uploadLogo'])->name('logo');
        });

        // Subscription
        Route::prefix('subscription')->name('subscription.')->group(function () {
            Route::get('/',                [SubscriptionController::class, 'show'])->name('show');
            Route::post('checkout',        [SubscriptionController::class, 'checkout'])->name('checkout');
            Route::post('billing-portal',  [SubscriptionController::class, 'billingPortal'])->name('billing-portal');
        });

        // Analytics
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('summary', [AnalyticsController::class, 'summary'])->name('summary');
            Route::get('usage',   [AnalyticsController::class, 'usage'])->name('usage');
        });

        // Templates
        Route::prefix('templates')->name('templates.')->group(function () {
            Route::get('/',      [TemplateController::class, 'index'])->name('index');
            Route::get('{id}',   [TemplateController::class, 'show'])->name('show');
        });

        // Users/Profile
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('me',    [UserController::class, 'show'])->name('me');
            Route::patch('me',  [UserController::class, 'update'])->name('update');
        });

        // Instagram OAuth
        Route::prefix('instagram')->name('instagram.')->group(function () {
            Route::get('auth-url', [InstagramOAuthController::class, 'authUrl'])->name('auth-url');
            Route::get('callback', [InstagramOAuthController::class, 'callback'])->name('callback');
        });

        // Social Accounts
        Route::apiResource('social-accounts', SocialAccountController::class)->only(['index', 'destroy']);

        // Scheduled Posts
        Route::apiResource('scheduled-posts', ScheduledPostController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

        // Admin — Knowledge Base
        Route::prefix('admin/knowledge-base')->name('admin.kb.')->group(function () {
            Route::get('food-categories', [FoodCategoryController::class, 'index'])->name('food-categories.index');
            Route::post('food-categories', [FoodCategoryController::class, 'store'])->name('food-categories.store');
            Route::patch('food-categories/{id}', [FoodCategoryController::class, 'update'])->name('food-categories.update');
            Route::delete('food-categories/{id}', [FoodCategoryController::class, 'destroy'])->name('food-categories.destroy');

            Route::get('cuisine-styles', [CuisineStyleController::class, 'index'])->name('cuisine-styles.index');
            Route::patch('cuisine-styles/{id}', [CuisineStyleController::class, 'update'])->name('cuisine-styles.update');

            Route::get('campaign-contexts', [CampaignContextController::class, 'index'])->name('campaign-contexts.index');
            Route::patch('campaign-contexts/{id}', [CampaignContextController::class, 'update'])->name('campaign-contexts.update');

            Route::post('cache/clear', function () {
                app(\App\Services\AI\FoodKnowledgeService::class)->clearCache();
                return response()->json(['message' => 'Cache cleared']);
            })->name('cache.clear');
        });

    });

});
