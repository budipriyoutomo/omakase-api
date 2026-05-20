<?php

declare(strict_types=1);

namespace App\Ai\Campaign\Intelligence;

use App\Ai\DTOs\CampaignPayloadDTO;

final class CampaignSignalClassifier
{
    public function campaignGoal(CampaignPayloadDTO $payload): string
    {
        return match ($this->normalize($payload->goal)) {
            'increase sales' => 'increase_sales',
            'boost awareness' => 'brand_awareness',
            'launch new menu' => 'menu_launch',
            'create viral campaign' => 'social_reach',
            default => 'balanced_campaign',
        };
    }

    public function conversionPriority(CampaignPayloadDTO $payload): string
    {
        $score = 0;

        if ($this->contains($payload->goal, ['sales', 'increase', 'conversion'])) {
            $score += 45;
        }

        if ($this->contains($payload->ctaStrategy, ['strong', 'urgency', 'sales'])) {
            $score += 40;
        }

        if ($this->contains($payload->platform, ['gofood', 'banner', 'story'])) {
            $score += 15;
        }

        return match (true) {
            $score >= 70 => 'high',
            $score >= 35 => 'medium',
            default => 'low',
        };
    }

    public function brandPositioning(CampaignPayloadDTO $payload): string
    {
        if ($this->contains($payload->mood.' '.$payload->audience.' '.$payload->style, ['luxury', 'high income', 'premium', 'elegant'])) {
            return 'luxury';
        }

        if ($this->contains($payload->mood.' '.$payload->style, ['cozy', 'family', 'warm'])) {
            return 'warm_accessible';
        }

        if ($this->contains($payload->audience.' '.$payload->mood, ['gen z', 'energetic', 'viral'])) {
            return 'culture_forward';
        }

        return 'commercial_mainstream';
    }

    public function audienceEnergy(CampaignPayloadDTO $payload): string
    {
        return match ($this->normalize($payload->audience)) {
            'gen z' => 'gen_z',
            'office workers' => 'efficient_lunch_decision',
            'family' => 'warm_group_dining',
            'high income customers' => 'premium_discerning',
            default => 'broad_restaurant_audience',
        };
    }

    public function ctaStrength(CampaignPayloadDTO $payload): string
    {
        return match (true) {
            $this->contains($payload->ctaStrategy, ['strong', 'urgency', 'sales']) => 'strong',
            $this->contains($payload->ctaStrategy, ['luxury', 'soft', 'minimal']) => 'soft_premium',
            blank($payload->ctaStrategy) => 'implicit',
            default => 'moderate',
        };
    }

    public function platformBehavior(CampaignPayloadDTO $payload): string
    {
        return match ($this->normalize($payload->platform)) {
            'tiktok', 'instagram story' => 'fast_scroll_mobile',
            'instagram feed' => 'aesthetic_grid_browse',
            'gofood banner' => 'delivery_purchase_scan',
            default => 'mobile_first_ad_view',
        };
    }

    public function marketingEnergy(CampaignPayloadDTO $payload): string
    {
        if ($this->contains($payload->mood.' '.$payload->goal, ['energetic', 'viral'])) {
            return 'high_momentum';
        }

        if ($this->contains($payload->mood.' '.$payload->style, ['luxury', 'elegant', 'minimal'])) {
            return 'controlled_premium';
        }

        if ($this->contains($payload->mood, ['cozy', 'family'])) {
            return 'warm_inviting';
        }

        return 'clear_commercial';
    }

    public function campaignTone(CampaignPayloadDTO $payload): string
    {
        return match (true) {
            $this->contains($payload->mood, ['luxury', 'elegant']) => 'refined_confident',
            $this->contains($payload->ctaStrategy, ['urgency']) => 'direct_immediate',
            $this->contains($payload->audience, ['gen z']) => 'bold_native_social',
            default => 'appetizing_commercial',
        };
    }

    public function psychologySignals(CampaignPayloadDTO $payload): array
    {
        return array_values(array_filter([
            $this->conversionPriority($payload) === 'high' ? 'reduce decision friction with obvious value cue' : null,
            $this->brandPositioning($payload) === 'luxury' ? 'signal scarcity, restraint, and premium craft' : null,
            $this->audienceEnergy($payload) === 'gen_z' ? 'reward fast recognition and visual novelty' : null,
            $this->platformBehavior($payload) === 'delivery_purchase_scan' ? 'optimize for menu clarity and immediate appetite trigger' : null,
            blank($payload->heroItem) ? 'infer dish hero from cuisine and user prompt' : 'anchor desire around the named hero item',
        ]));
    }

    private function normalize(string $value): string
    {
        return strtolower(trim($value));
    }

    private function contains(string $haystack, array $needles): bool
    {
        $normalized = $this->normalize($haystack);

        foreach ($needles as $needle) {
            if (str_contains($normalized, $needle)) {
                return true;
            }
        }

        return false;
    }
}
