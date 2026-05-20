<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // Trending
            ['name' => 'Promo Flash Sale', 'category' => 'promotion', 'style' => 'urgent', 'is_trending' => true],
            ['name' => 'New Menu Launch', 'category' => 'announcement', 'style' => 'exciting', 'is_trending' => true],
            ['name' => 'Weekend Special', 'category' => 'promotion', 'style' => 'casual', 'is_trending' => true],
            ['name' => 'Customer Testimonial', 'category' => 'social_proof', 'style' => 'warm', 'is_trending' => true],
            ['name' => 'Chef Feature Story', 'category' => 'branding', 'style' => 'storytelling', 'is_trending' => true],
            ['name' => 'Holiday Campaign', 'category' => 'seasonal', 'style' => 'festive', 'is_trending' => true],
            // Regular
            ['name' => 'Happy Hour Alert', 'category' => 'promotion', 'style' => 'energetic', 'is_trending' => false],
            ['name' => 'Loyalty Rewards', 'category' => 'retention', 'style' => 'friendly', 'is_trending' => false],
            ['name' => 'Event Invitation', 'category' => 'event', 'style' => 'elegant', 'is_trending' => false],
            ['name' => 'Instagram Reel Script', 'category' => 'video', 'style' => 'trendy', 'is_trending' => false],
            ['name' => 'Google Ads Copy', 'category' => 'paid_ads', 'style' => 'conversion', 'is_trending' => false],
            ['name' => 'Email Newsletter', 'category' => 'email', 'style' => 'informative', 'is_trending' => false],
        ];

        foreach ($templates as $template) {
            Template::updateOrCreate(
                ['name' => $template['name']],
                array_merge(['id' => Str::uuid()], $template)
            );
        }
    }
}
