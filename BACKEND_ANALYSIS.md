# 🍣 Omakase API — Backend Flow & Architecture

> **Generated:** 13/06/2026  
> **Stack:** Laravel 11+ (PHP 8.2+) • JWT Auth • Replicate AI • MySQL/PostgreSQL

---

## 📁 Struktur Direktori

```
omakase-api/
├── app/
│   ├── Ai/                          # ⭐ AI Core — orchestrator, pipelines, renderers, agents
│   │   ├── Agents/                  #   Visual director agents per campaign type
│   │   │   ├── CommercialFoodDirector.php
│   │   │   ├── DeliveryBannerDirector.php
│   │   │   ├── LuxuryHospitalityDirector.php
│   │   │   └── OmakaseVisionDirector.php
│   │   ├── Campaign/                #   Campaign intelligence: signals, strategies, DTOs
│   │   │   ├── DTOs/CampaignIntelligenceDTO.php
│   │   │   ├── Intelligence/CampaignSignalClassifier.php
│   │   │   ├── Strategies/
│   │   │   └── Support/
│   │   ├── Contracts/               #   Interfaces (ArrayableData, VisualDirector, Provider)
│   │   ├── Creative/                #   Creative HTML blueprint assembler + themes
│   │   │   ├── CreativeBlueprintAssembler.php
│   │   │   ├── ThemeRegistry.php
│   │   │   ├── ThemeResolver.php
│   │   │   ├── Agents/ThemeDetectorAgent.php
│   │   │   ├── Blueprints/CreativeBlueprintDTO.php
│   │   │   ├── Components/          #   HTML components (HeroHeadline, LuxuryCTA, dll.)
│   │   │   ├── Layouts/LayoutMode.php
│   │   │   ├── Themes/              #   FineDiningTheme, DeliveryPromoTheme
│   │   │   └── Tokens/
│   │   ├── DTOs/                    #   Data Transfer Objects
│   │   │   ├── AiGenerationLifecycleDTO.php
│   │   │   ├── CampaignPayloadDTO.php
│   │   │   ├── ImageGenerationDTO.php
│   │   │   ├── ImageGenerationPayloadDTO.php       # Visual-only payload
│   │   │   ├── MarketingIntelligencePayloadDTO.php  # Marketing-only payload
│   │   │   └── PromptRenderResultDTO.php
│   │   ├── Exceptions/
│   │   ├── Orchestrators/           #   Orchestration layer
│   │   │   ├── CampaignIntelligenceOrchestrator.php
│   │   │   ├── PromptOrchestrator.php
│   │   │   └── VisualCompositionOrchestrator.php
│   │   ├── Pipelines/               #   Processing pipelines
│   │   │   ├── AnalyticsPipeline.php
│   │   │   ├── ImageGenerationPipeline.php
│   │   │   ├── PromptGenerationPipeline.php
│   │   │   └── StoragePipeline.php
│   │   ├── Prompts/                 #   (direktori kosong/tidak aktif)
│   │   ├── Providers/
│   │   │   └── ReplicateProvider.php
│   │   ├── Renderers/               #   Prompt & HTML renderers
│   │   │   ├── CommercialPromptRenderer.php
│   │   │   ├── ImagePromptRenderer.php
│   │   │   ├── Contracts/
│   │   │   ├── Html/                #   HtmlCreativeRenderer
│   │   │   └── Support/
│   │   ├── Routers/
│   │   │   └── VisualDirectionRouter.php
│   │   ├── Services/                #   AI services
│   │   │   ├── ImageGenerationService.php
│   │   │   ├── ImageStorageService.php
│   │   │   └── PromptGenerationService.php
│   │   ├── Typography/              #   Typography vision & DTOs
│   │   │   ├── DTOs/TypographyBlueprintDTO.php
│   │   │   └── Vision/TypographyVisionAgent.php
│   │   ├── ValueObjects/
│   │   └── Visual/                  #   Visual composition & photography
│   │       ├── Composition/
│   │       ├── DTOs/
│   │       ├── Intelligence/
│   │       ├── Layout/
│   │       ├── Photography/
│   │       └── Support/
│   │
│   ├── Console/Commands/            # Artisan commands
│   │   ├── ClearFoodKnowledgeCache.php
│   │   └── SchedulePostsCommand.php
│   │
│   ├── Exceptions/
│   │   └── InstagramPublishException.php
│   │
│   ├── Http/Controllers/
│   │   ├── Controller.php
│   │   └── GeneratePromptController.php  ⚠️ UNUSED
│   │
│   ├── Models/                      # Eloquent models
│   │   ├── BrandKit.php
│   │   ├── CampaignContext.php
│   │   ├── CuisineStyle.php
│   │   ├── FoodCategory.php
│   │   ├── Generation.php
│   │   ├── PostAnalytics.php
│   │   ├── ScheduledPost.php
│   │   ├── SocialAccount.php
│   │   ├── SubscriptionPlan.php
│   │   ├── Template.php
│   │   ├── User.php
│   │   └── UserSubscription.php
│   │
│   ├── Modules/                     # Domain modules (Controller + Service + Repository)
│   │   ├── Analytics/
│   │   ├── Auth/
│   │   ├── BrandKit/
│   │   ├── Dashboard/
│   │   ├── Generation/              # ⭐ Core generation module
│   │   │   ├── Actions/
│   │   │   ├── Agents/
│   │   │   ├── Controllers/GenerationController.php
│   │   │   ├── DTOs/CreateGenerationDTO.php
│   │   │   ├── Events/GenerationCreated.php
│   │   │   ├── Jobs/
│   │   │   │   ├── ProcessGenerationJob.php    # Main AI processing job
│   │   │   │   └── CheckPredictionJob.php      # Polling job untuk Replicate
│   │   │   ├── Repositories/GenerationRepository.php
│   │   │   ├── Requests/CreateGenerationRequest.php
│   │   │   ├── Resources/GenerationResource.php
│   │   │   └── Services/GenerationService.php
│   │   ├── KnowledgeBase/           # Food categories, cuisine styles, campaign contexts
│   │   ├── Social/                  # Instagram OAuth + Scheduled Posts
│   │   ├── Subscription/
│   │   ├── Template/
│   │   └── User/
│   │
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   │
│   ├── Services/
│   │   ├── AI/FoodKnowledgeService.php
│   │   └── Instagram/
│   │       ├── InstagramOAuthService.php
│   │       └── InstagramPublishingService.php
│   │
│   └── Shared/                      # Shared contracts, DTOs, traits, middleware
│       ├── Contracts/
│       ├── DTOs/PaginationDTO.php
│       ├── Exceptions/
│       ├── Http/
│       └── Traits/ApiResponseTrait.php
│
├── bootstrap/
├── config/
├── database/
│   ├── migrations/                  # 7 migration files
│   └── seeders/                     # FoodCategorySeeder, CuisineStyleSeeder, CampaignContextSeeder
├── routes/
│   ├── api.php                      # ⭐ All API routes (v1)
│   └── console.php                  # Artisan command scheduling
├── storage/
└── tests/
```

---

## 🔄 Flow Utama: AI Image Generation

### Diagram Flow

```
┌─────────────────────────────────────────────────────────────────────┐
│                         FRONTEND (omakase-ai)                       │
│                                                                     │
│  generator-form.tsx  ──POST /v1/generations──►  types.ts           │
│  generate/page.tsx                              CreateGenerationPayload
│  history/page.tsx                               GenerationRecord
└─────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      BACKEND (omakase-api)                          │
│                                                                     │
│  routes/api.php:v1/generations                                     │
│         │                                                           │
│         ▼                                                           │
│  GenerationController.store()                                       │
│         │                                                           │
│         ▼                                                           │
│  GenerationService.create()                                        │
│         │                                                           │
│         ├── DB INSERT → status: pending                             │
│         ├── event: GenerationCreated                                │
│         └── dispatch: ProcessGenerationJob                          │
│                            │                                        │
│                            ▼                                        │
│  ┌─────────────────── QUEUE: ai-generation ───────────────────┐    │
│  │                                                             │    │
│  │  ProcessGenerationJob.handle()                              │    │
│  │         │                                                   │    │
│  │         ├── status → processing                             │    │
│  │         │                                                   │    │
│  │         ├── 1️⃣ ImageGenerationPayloadDTO.fromGeneration()  │    │
│  │         │   (visual fields only: style, mood, heroItem,     │    │
│  │         │    cuisine, aspectRatio, prompt, negativePrompt)   │    │
│  │         │                                                   │    │
│  │         ├── 2️⃣ MarketingIntelligencePayloadDTO (terpisah)   │    │
│  │         │   └── CampaignIntelligenceOrchestrator             │    │
│  │         │       .buildFromMarketing()                       │    │
│  │         │       → CampaignIntelligenceDTO (non-blocking)     │    │
│  │         │                                                   │    │
│  │         ├── 3️⃣ PromptGenerationPipeline.handleForImage()    │    │
│  │         │   └── PromptGenerationService.generateForImage()  │    │
│  │         │       └── PromptOrchestrator.orchestrateForImage() │    │
│  │         │           ├── CampaignIntelligenceOrchestrator     │    │
│  │         │           ├── VisualCompositionOrchestrator        │    │
│  │         │           └── ImagePromptRenderer                  │    │
│  │         │           → AiGenerationLifecycleDTO               │    │
│  │         │                                                   │    │
│  │         ├── event: VisualOrchestrationRendered               │    │
│  │         │                                                   │    │
│  │         ├── 4️⃣ ImageGenerationPipeline.dispatch()           │    │
│  │         │   └── ImageGenerationService.generate()            │    │
│  │         │       └── ReplicateProvider.generate()             │    │
│  │         │         → Replicate API (Flux Schnell)             │    │
│  │         │                                                   │    │
│  │         ├── 5️⃣ AnalyticsPipeline.metadataForQueuedImage()   │    │
│  │         │                                                   │    │
│  │         └── status → generated_image                        │    │
│  │              └── dispatch: CheckPredictionJob (delay 5s)     │    │
│  └─────────────────────────────────────────────────────────────┘    │
│                                                                     │
│  ┌─────────────────── QUEUE: default ─────────────────────────┐    │
│  │                                                             │    │
│  │  CheckPredictionJob.handle()                                │    │
│  │         │                                                   │    │
│  │         ├── Poll Replicate prediction status                │    │
│  │         ├── Jika masih processing → re-dispatch (delay)      │    │
│  │         └── Jika selesai → update image_url,                │    │
│  │                             status → completed              │    │
│  └─────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────┘
```

### Pipeline Detail: PromptEnhancement

```
PromptGenerationService.generateForImage(ImageGenerationPayloadDTO)
  │
  ▼
PromptOrchestrator.orchestrateForImage()
  │
  ├── CampaignIntelligenceOrchestrator.build()
  │     └── CampaignSignalClassifier → CampaignIntelligenceDTO
  │
  ├── VisualCompositionOrchestrator.build()
  │     ├── VisualDirectionRouter → pilih director agent
  │     │     ├── CommercialFoodDirector
  │     │     ├── DeliveryBannerDirector
  │     │     ├── LuxuryHospitalityDirector
  │     │     └── OmakaseVisionDirector
  │     └── → VisualIntelligenceDTO (composition, lighting, style)
  │
  └── ImagePromptRenderer.render()
        → Enhanced prompt + negative prompt
```

---

## 🎨 Flow Creative HTML & Typography

```
POST /v1/generations/{id}/creative/render
  │
  ▼
GenerationController.renderCreative()
  │
  ├── Merge custom typography dari request body
  │     ├── headline: text, font_size, font_family, position
  │     ├── subheadline: text, font_size
  │     ├── cta: text, font_size
  │     └── font_pairing: headline, body
  │
  ├── Build CreativeBlueprintDTO
  │
  ├── HtmlCreativeRenderer.render(blueprintDTO, imageUrl)
  │     └── CreativeBlueprintAssembler
  │           ├── ThemeResolver (FineDiningTheme / DeliveryPromoTheme)
  │           ├── ComponentFactory → render components:
  │           │     ├── HeroHeadline
  │           │     ├── SubHeadline
  │           │     ├── LuxuryCTA
  │           │     ├── LaunchBadge
  │           │     ├── ReservationBadge
  │           │     ├── EditorialDivider
  │           │     └── JapaneseVerticalText
  │           └── Overlay strategy + typography tokens
  │
  └── Persist creative_html + creative_blueprint + typography_blueprint
        ke ai_metadata JSON column
```

### Preview HTML (read-only)
```
GET /v1/generations/{id}/creative
  → Kembalikan creative_html dari ai_metadata sebagai text/html
  → Headers: X-Creative-Theme, X-Creative-Layout
```

---

## 📱 Flow Instagram Social Publishing

```
┌──────────────────────────────────────────────────────────┐
│                    Instagram Flow                         │
│                                                          │
│  1. Connect Account                                      │
│     GET  /v1/instagram/auth-url                          │
│          └── InstagramOAuthService → Meta OAuth URL       │
│                                                          │
│  2. Callback                                             │
│     GET  /v1/instagram/callback?code=xxx                 │
│          └── Exchange code → access token                │
│          └── Store SocialAccount                         │
│                                                          │
│  3. Schedule Post                                        │
│     POST /v1/scheduled-posts                             │
│          └── Validasi: CreateScheduledPostRequest         │
│          └── Simpan ScheduledPost (status: pending)      │
│                                                          │
│  4. Auto-publish (via Scheduler)                         │
│     SchedulePostsCommand (artisan schedule:work)          │
│     └── Ambil post yg scheduled_at <= now                │
│     └── PublishScheduledPostJob                          │
│          └── InstagramPublishingService.publish()         │
│               ├── Upload media ke Instagram               │
│               ├── Create post dengan caption + hashtags   │
│               └── Update PostAnalytics                    │
│                                                          │
│  5. Manage                                               │
│     GET    /v1/social-accounts    (list connected)        │
│     DELETE /v1/social-accounts/{id} (disconnect)         │
│     GET    /v1/scheduled-posts    (list)                  │
│     PATCH  /v1/scheduled-posts/{id} (update/reschedule)  │
│     DELETE /v1/scheduled-posts/{id} (cancel)              │
└──────────────────────────────────────────────────────────┘
```

---

## 🗄️ Database Schema

| Table | Model | Deskripsi |
|-------|-------|-----------|
| `users` | `User` | JWT auth users |
| `generations` | `Generation` | ⭐ Hasil AI generation. Kolom: campaign_type, cuisine, platform, audience, goal, mood, style, hero_item, visual_strategy, cta_strategy, aspect_ratio, prompt, enhanced_prompt, negative_prompt, orchestration (JSON), agent, provider, model, result, image_url, image_urls, preview_urls, tokens_used, cost, generation_time_ms, metadata (JSON), ai_metadata (JSON), raw_response, status |
| `templates` | `Template` | Template kampanye + payload |
| `brands_kits` | `BrandKit` | Brand identity per user (logo, warna, font) |
| `subscription_plans` | `SubscriptionPlan` | Paket subscription |
| `user_subscriptions` | `UserSubscription` | Relasi user-plan |
| `social_accounts` | `SocialAccount` | Connected Instagram accounts |
| `scheduled_posts` | `ScheduledPost` | Post terjadwal (caption, hashtags, image_url, scheduled_at, status) |
| `post_analytics` | `PostAnalytics` | Metrik post Instagram |
| `food_categories` | `FoodCategory` | Knowledge base: kategori makanan |
| `cuisine_styles` | `CuisineStyle` | Knowledge base: style masakan |
| `campaign_contexts` | `CampaignContext` | Knowledge base: konteks campaign |

### Status Generation Lifecycle

```
pending → processing → generated_image → completed
                                          ↘ failed (any stage)
```

---

## 🚦 Complete Route Map

### Public Routes (No Auth)
| Method | Endpoint | Controller | Deskripsi |
|--------|----------|-----------|-----------|
| POST | `/v1/auth/login` | AuthController@login | Login JWT |
| POST | `/v1/auth/register` | AuthController@register | Register |
| POST | `/v1/auth/forgot-password` | AuthController@forgotPassword | Lupa password |
| POST | `/v1/auth/refresh` | AuthController@refresh | Refresh token |
| GET | `/v1/subscription/plans` | SubscriptionController@plans | List paket |

### Protected Routes (JWT Required)

| Method | Endpoint | Controller | Deskripsi |
|--------|----------|-----------|-----------|
| **Auth** ||||
| POST | `/v1/auth/logout` | AuthController@logout | Logout |
| GET | `/v1/auth/me` | AuthController@me | Current user |
| POST | `/v1/auth/change-password` | AuthController@changePassword | Ubah password |
| POST | `/v1/auth/me/avatar` | AuthController@uploadAvatar | Upload avatar |
| **Dashboard** ||||
| GET | `/v1/dashboard/stats` | DashboardController@stats | Stats dashboard |
| GET | `/v1/dashboard/usage` | DashboardController@usage | Usage bulanan |
| GET | `/v1/dashboard/suggestions` | DashboardController@suggestions | Saran AI |
| GET | `/v1/dashboard/templates/trending` | DashboardController@trendingTemplates | Template populer |
| **Generations** ⭐ ||||
| GET | `/v1/generations` | GenerationController@index | List history |
| POST | `/v1/generations` | GenerationController@store | Buat generation baru |
| GET | `/v1/generations/{id}` | GenerationController@show | Detail generation |
| DELETE | `/v1/generations/{id}` | GenerationController@destroy | Hapus |
| POST | `/v1/generations/{id}/duplicate` | GenerationController@duplicate | Duplikasi |
| POST | `/v1/generations/{id}/regenerate` | GenerationController@regenerate | Regenerasi ulang |
| POST | `/v1/generations/{id}/creative` | GenerationController@creative | Lihat creative HTML |
| POST | `/v1/generations/{id}/creative/render` | GenerationController@renderCreative | Re-render dengan custom typography |
| **Brand Kit** ||||
| GET | `/v1/brand-kit` | BrandKitController@show | Brand kit saat ini |
| PATCH | `/v1/brand-kit` | BrandKitController@update | Update brand kit |
| POST | `/v1/brand-kit/logo` | BrandKitController@uploadLogo | Upload logo |
| **Subscription** ||||
| GET | `/v1/subscription` | SubscriptionController@show | Status subscription |
| POST | `/v1/subscription/checkout` | SubscriptionController@checkout | Checkout |
| POST | `/v1/subscription/billing-portal` | SubscriptionController@billingPortal | Billing portal |
| **Analytics** ||||
| GET | `/v1/analytics/summary` | AnalyticsController@summary | Ringkasan analytics |
| GET | `/v1/analytics/usage` | AnalyticsController@usage | Detail usage |
| **Templates** ||||
| GET | `/v1/templates` | TemplateController@index | List templates |
| GET | `/v1/templates/{id}` | TemplateController@show | Detail template |
| **Users** ||||
| GET | `/v1/users/me` | UserController@show | Current user profile |
| PATCH | `/v1/users/me` | UserController@update | Update profile |
| **Instagram OAuth** ||||
| GET | `/v1/instagram/auth-url` | InstagramOAuthController@authUrl | Dapatkan OAuth URL |
| GET | `/v1/instagram/callback` | InstagramOAuthController@callback | OAuth callback |
| **Social Accounts** ||||
| GET | `/v1/social-accounts` | SocialAccountController@index | List akun tersambung |
| DELETE | `/v1/social-accounts/{id}` | SocialAccountController@destroy | Putuskan akun |
| **Scheduled Posts** ||||
| GET | `/v1/scheduled-posts` | ScheduledPostController@index | List post terjadwal |
| GET | `/v1/scheduled-posts/{id}` | ScheduledPostController@show | Detail post |
| POST | `/v1/scheduled-posts` | ScheduledPostController@store | Buat post terjadwal |
| PATCH | `/v1/scheduled-posts/{id}` | ScheduledPostController@update | Update post |
| DELETE | `/v1/scheduled-posts/{id}` | ScheduledPostController@destroy | Batalkan post |
| **Admin: Knowledge Base** ||||
| GET | `/v1/admin/knowledge-base/food-categories` | FoodCategoryController@index | List kategori |
| POST | `/v1/admin/knowledge-base/food-categories` | FoodCategoryController@store | Tambah |
| PATCH | `/v1/admin/knowledge-base/food-categories/{id}` | FoodCategoryController@update | Update |
| DELETE | `/v1/admin/knowledge-base/food-categories/{id}` | FoodCategoryController@destroy | Hapus |
| GET | `/v1/admin/knowledge-base/cuisine-styles` | CuisineStyleController@index | List style |
| PATCH | `/v1/admin/knowledge-base/cuisine-styles/{id}` | CuisineStyleController@update | Update |
| GET | `/v1/admin/knowledge-base/campaign-contexts` | CampaignContextController@index | List context |
| PATCH | `/v1/admin/knowledge-base/campaign-contexts/{id}` | CampaignContextController@update | Update |
| POST | `/v1/admin/knowledge-base/cache/clear` | Closure | Clear FoodKnowledge cache |

---

## 🗑️ File yang Tidak Digunakan

### ⛔ Benar-benar Tidak Digunakan (No Route/No Caller)

| File | Alasan |
|------|--------|
| **`app/Http/Controllers/GeneratePromptController.php`** | Tidak ada route di `api.php` atau `console.php` yang mengarah ke controller ini. Fungsionalitas generate prompt sudah sepenuhnya ditangani oleh `GenerationController` → `ProcessGenerationJob` → `PromptGenerationPipeline`. |

### ⚠️ Deprecated (Masih Ada, Tapi Ada Penggantinya)

| File / Method | Keterangan |
|--------------|------------|
| `PromptOrchestrator::build()` | Tag `@deprecated` — gunakan `orchestrateForImage()` |
| `PromptOrchestrator::orchestrate()` | Tag `@deprecated` — dipanggil oleh `build()` yang juga deprecated |
| `PromptGenerationPipeline::handle()` | Tag `@deprecated` — gunakan `handleForImage()` |
| `PromptGenerationService::generate()` | Hanya dipanggil oleh `PromptGenerationPipeline::handle()` (deprecated) dan `GeneratePromptController` (unused) |

### 📂 Direktori Kosong / Tidak Aktif

| Direktori | Status |
|-----------|--------|
| `app/Ai/Prompts/` | Tidak ada file PHP di dalamnya (perlu verifikasi) |

---

## 🔗 Cross-Reference: Backend ↔ Frontend

| Frontend File | Backend Endpoint |
|--------------|-----------------|
| `lib/api/social.ts` | `/v1/instagram/*`, `/v1/social-accounts`, `/v1/scheduled-posts` |
| `lib/services/campaigns.service.ts` | `/v1/generations` |
| `lib/api/types.ts` | Semua DTO response type |
| `app/dashboard/generate/page.tsx` | `POST /v1/generations` |
| `app/dashboard/generate/preview/[id]/page.tsx` | `GET /v1/generations/{id}`, `POST .../creative/render` |
| `app/dashboard/history/page.tsx` | `GET /v1/generations` |
| `app/dashboard/templates/page.tsx` | `GET /v1/templates` |
| `app/dashboard/subscription/page.tsx` | `/v1/subscription/*` |
| `app/dashboard/social-accounts/page.tsx` | `/v1/social-accounts` |
| `app/dashboard/scheduled-posts/` | `/v1/scheduled-posts` |
| `app/dashboard/instagram/callback/` | `/v1/instagram/callback` |
| `components/generator/generator-form.tsx` | `POST /v1/generations` |
| `components/generator/typography-preview.tsx` | `POST .../creative/render` |
| `components/social/schedule-post-modal.tsx` | `POST /v1/scheduled-posts` |

---

## 📊 Status Summary

| Komponen | Status |
|----------|--------|
| AI Image Generation | ✅ Active (main flow) |
| Creative HTML Rendering | ✅ Active |
| Typography Preview | ✅ Active |
| Marketing Intelligence | ✅ Active (non-blocking, terpisah) |
| Instagram OAuth | ✅ Active |
| Scheduled Posts | ✅ Active |
| Knowledge Base | ✅ Active (admin only) |
| Brand Kit | ✅ Active |
| Subscription | ✅ Active |
| Dashboard | ✅ Active |
| Analytics | ✅ Active |
| GeneratePromptController | ❌ Unused — bisa dihapus |
| Deprecated orchestration methods | ⚠️ Bisa dibersihkan saat refactor |

---

> **Kesimpulan:** Backend omakase-api menggunakan arsitektur **Module-based + AI Orchestration Pipeline**. Flow utama: User mengirim payload campaign → GenerationController → GenerationService → ProcessGenerationJob (Queue) → PromptOrchestrator (enhance prompt) → Replicate (generate image) → CheckPredictionJob (polling). Marketing intelligence diproses terpisah secara non-blocking. Creative HTML & typography di-render via CreativeBlueprintAssembler + HtmlCreativeRenderer.
>
> **File yang bisa dihapus:** `app/Http/Controllers/GeneratePromptController.php` (tidak ada route). **Method deprecated** di `PromptOrchestrator`, `PromptGenerationPipeline`, dan `PromptGenerationService` bisa dibersihkan saat refactor berikutnya.