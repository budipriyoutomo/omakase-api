<?php

declare(strict_types=1);

namespace App\Ai\Visual\Composition;

use App\Ai\Campaign\DTOs\CampaignIntelligenceDTO;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\Visual\DTOs\VisualIntelligenceDTO;

final class VisualCompositionEngine
{
    public function compose(
        CampaignPayloadDTO $payload,
        CampaignIntelligenceDTO $campaign
    ): VisualIntelligenceDTO {
        $platformBehavior = $campaign->platformBehavior;
        $brandPositioning = $campaign->brandPositioning;

        return new VisualIntelligenceDTO(
            heroFocus: $this->heroFocus($payload, $campaign),
            compositionType: $this->compositionType($payload, $campaign),
            campaignVisualFormat: $this->campaignVisualFormat($payload),
            realismLevel: $this->realismLevel($payload, $campaign),
            photographyStyle: $this->photographyStyle($payload, $campaign),
            cameraAngle: $this->cameraAngle($payload, $platformBehavior),
            lighting: $this->lighting($payload, $brandPositioning),
            negativeSpace: $this->negativeSpace($payload, $campaign),
            typographySafeLayout: $this->typographySafeLayout($payload, $campaign),
            ctaSafeSpacing: $this->ctaSafeSpacing($payload, $campaign),
            foodTexture: $this->foodTexture($payload),
            layoutStrategy: $this->layoutStrategy($payload, $campaign),
            visualHierarchy: $this->visualHierarchy($payload, $campaign),
            director: 'unassigned',
            commercialDirectives: $this->commercialDirectives($payload, $campaign),
            metadata: [
                'layer' => 'visual_intelligence',
                'routing_basis' => [
                    'visual_direction',
                    'composition_type',
                    'campaign_visual_format',
                    'realism_level',
                    'photography_style',
                    'platform_visual_behavior',
                ],
            ],
        );
    }

    private function heroFocus(CampaignPayloadDTO $payload, CampaignIntelligenceDTO $campaign): string
    {
        if ($campaign->conversionPriority === 'high' || filled($payload->heroItem)) {
            return 'food-first';
        }

        return $campaign->brandPositioning === 'luxury'
            ? 'hospitality-atmosphere-with-food-dominance'
            : 'food-led-brand-scene';
    }

    private function compositionType(CampaignPayloadDTO $payload, CampaignIntelligenceDTO $campaign): string
    {
        if ($campaign->platformBehavior === 'delivery_purchase_scan') {
            return 'delivery-banner-product-grid';
        }

        if ($campaign->brandPositioning === 'luxury') {
            return 'premium-negative-space-hero';
        }

        if ($campaign->platformBehavior === 'fast_scroll_mobile') {
            return 'mobile-centered-hero-impact';
        }

        return 'commercial-food-hero-layout';
    }

    private function campaignVisualFormat(CampaignPayloadDTO $payload): string
    {
        return match (strtolower(trim($payload->platform))) {
            'instagram story', 'tiktok' => 'vertical-mobile-ad',
            'gofood banner' => 'horizontal-delivery-banner',
            'instagram feed' => 'square-social-feed',
            default => 'mobile-first-restaurant-ad',
        };
    }

    private function realismLevel(CampaignPayloadDTO $payload, CampaignIntelligenceDTO $campaign): string
    {
        if ($campaign->brandPositioning === 'luxury') {
            return 'premium-commercial-realism';
        }

        if (str_contains(strtolower($payload->style), 'illustration')) {
            return 'stylized-commercial-realism';
        }

        return 'macro-food-photorealism';
    }

    private function photographyStyle(CampaignPayloadDTO $payload, CampaignIntelligenceDTO $campaign): string
    {
        if ($campaign->brandPositioning === 'luxury') {
            return 'luxury-hospitality-editorial';
        }

        if ($campaign->platformBehavior === 'delivery_purchase_scan') {
            return 'clean-delivery-commerce-photography';
        }

        return 'commercial-food-advertising-photography';
    }

    private function cameraAngle(CampaignPayloadDTO $payload, string $platformBehavior): string
    {
        $text = strtolower($payload->visualStrategy.' '.$payload->prompt.' '.$payload->cuisine);

        if (str_contains($text, 'pizza') || str_contains($text, 'padang') || $platformBehavior === 'delivery_purchase_scan') {
            return 'top-down';
        }

        if (str_contains($text, 'burger') || str_contains($text, 'ramen') || str_contains($text, 'drink')) {
            return '45-degree-tabletop';
        }

        return 'three-quarter-commercial-hero';
    }

    private function lighting(CampaignPayloadDTO $payload, string $brandPositioning): string
    {
        if ($brandPositioning === 'luxury') {
            return 'soft cinematic side light with controlled shadows';
        }

        if (str_contains(strtolower($payload->mood), 'cozy')) {
            return 'warm ambient restaurant light';
        }

        return 'bright appetizing commercial softbox';
    }

    private function negativeSpace(CampaignPayloadDTO $payload, CampaignIntelligenceDTO $campaign): string
    {
        if ($campaign->ctaStrength === 'strong') {
            return $this->isVertical($payload) ? 'upper-third' : 'right-third';
        }

        if ($campaign->brandPositioning === 'luxury') {
            return 'top-right-minimal';
        }

        return $this->isVertical($payload) ? 'top-center' : 'right-side';
    }

    private function typographySafeLayout(CampaignPayloadDTO $payload, CampaignIntelligenceDTO $campaign): string
    {
        if ($campaign->platformBehavior === 'delivery_purchase_scan') {
            return 'clear right-side offer area with uncluttered background';
        }

        return $this->isVertical($payload)
            ? 'safe top and bottom zones for mobile overlay text'
            : 'clean lateral text area outside hero dish silhouette';
    }

    private function ctaSafeSpacing(CampaignPayloadDTO $payload, CampaignIntelligenceDTO $campaign): string
    {
        return $campaign->ctaStrength === 'strong'
            ? 'reserved high-contrast CTA block outside food texture'
            : 'subtle premium CTA breathing room';
    }

    private function foodTexture(CampaignPayloadDTO $payload): string
    {
        $text = strtolower($payload->prompt.' '.$payload->heroItem.' '.$payload->cuisine);

        return match (true) {
            str_contains($text, 'fried') || str_contains($text, 'crispy') => 'crispy macro realism',
            str_contains($text, 'ramen') || str_contains($text, 'soup') => 'steam and glossy broth realism',
            str_contains($text, 'sushi') || str_contains($text, 'omakase') => 'precise fresh fish sheen and rice grain detail',
            str_contains($text, 'coffee') || str_contains($text, 'bakery') => 'buttery crumb texture and crema realism',
            default => 'appetizing macro texture realism',
        };
    }

    private function layoutStrategy(CampaignPayloadDTO $payload, CampaignIntelligenceDTO $campaign): string
    {
        return match ($campaign->platformBehavior) {
            'fast_scroll_mobile' => 'mobile-centered thumb-stopping hero',
            'aesthetic_grid_browse' => 'instagram-centered premium crop',
            'delivery_purchase_scan' => 'delivery marketplace banner clarity',
            default => 'mobile-first commercial hierarchy',
        };
    }

    private function visualHierarchy(CampaignPayloadDTO $payload, CampaignIntelligenceDTO $campaign): string
    {
        return $campaign->conversionPriority === 'high'
            ? '70_percent_hero_dish_20_percent_offer_10_percent_brand'
            : '70_percent_hero_dish_20_percent_mood_10_percent_brand';
    }

    private function commercialDirectives(CampaignPayloadDTO $payload, CampaignIntelligenceDTO $campaign): array
    {
        return [
            'make the hero food immediately readable at mobile thumbnail size',
            'keep text-safe negative space clean and low-detail',
            'avoid AI-looking plating, deformed ingredients, fake typography, and clutter',
            'prioritize edible texture, realistic steam, sauce gloss, and natural shadows',
            $campaign->ctaStrength === 'strong'
                ? 'reserve a decisive CTA zone without covering the hero dish'
                : 'preserve premium restraint and breathing room',
        ];
    }

    private function isVertical(CampaignPayloadDTO $payload): bool
    {
        return in_array(strtolower(trim($payload->platform)), ['instagram story', 'tiktok'], true)
            || in_array(trim($payload->aspectRatio), ['9:16', '4:5'], true);
    }
}
