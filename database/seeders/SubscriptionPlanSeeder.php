<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'id'                => Str::uuid(),
                'name'              => 'Free',
                'price_usd_monthly' => 0,
                'credits_monthly'   => 10,
                'description'       => 'Get started with 10 AI generations per month. Perfect for individuals.',
                'is_active'         => true,
            ],
            [
                'id'                => Str::uuid(),
                'name'              => 'Starter',
                'price_usd_monthly' => 19,
                'credits_monthly'   => 100,
                'description'       => 'Ideal for small businesses. 100 generations and brand kit support.',
                'is_active'         => true,
            ],
            [
                'id'                => Str::uuid(),
                'name'              => 'Pro',
                'price_usd_monthly' => 49,
                'credits_monthly'   => 500,
                'description'       => 'For growing teams. Unlimited templates, priority generation, analytics.',
                'is_active'         => true,
            ],
            [
                'id'                => Str::uuid(),
                'name'              => 'Enterprise',
                'price_usd_monthly' => 149,
                'credits_monthly'   => null,
                'description'       => 'Unlimited generations, dedicated support, custom brand integrations.',
                'is_active'         => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['name' => $plan['name']], $plan);
        }
    }
}
