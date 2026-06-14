<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use App\Models\Template;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SubscriptionPlanSeeder::class,
            TemplateSeeder::class,
            FoodCategorySeeder::class,
            CuisineStyleSeeder::class,
            CampaignContextSeeder::class,
        ]);
    }
}
