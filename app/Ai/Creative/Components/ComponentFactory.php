<?php

declare(strict_types=1);

namespace App\Ai\Creative\Components;

use App\Ai\Creative\Components\Contracts\ComponentInterface;
use App\Ai\Creative\Tokens\DesignTokens;
use App\Ai\DTOs\CampaignPayloadDTO;
use App\Ai\Typography\DTOs\TypographyBlueprintDTO;

final class ComponentFactory
{
    public function build(
        TypographyBlueprintDTO $typography,
        DesignTokens           $tokens,
        CampaignPayloadDTO     $campaign,
    ): array {
        $components = [];

        /*
        |--------------------------------------------------------------------------
        | LAUNCH BADGE
        | Rendered first — sits above headline
        |--------------------------------------------------------------------------
        */

        $badge = $this->resolveBadgeText($campaign);

        if ($badge) {
            $components[] = new LaunchBadge(
                text: $badge,
                x:    (int) ($typography->headline['safe_area']['x'] ?? 70),
                y:    (int) ($typography->headline['safe_area']['y'] ?? 70),
            );
        }

        /*
        |--------------------------------------------------------------------------
        | EDITORIAL DIVIDER
        | Rendered before headline for luxury/fine dining themes
        |--------------------------------------------------------------------------
        */

        $components[] = new EditorialDivider(
            x:       (int) ($typography->headline['safe_area']['x'] ?? 70),
            y:       (int) ($typography->headline['safe_area']['y'] ?? 70) + ($badge ? 48 : 0),
            width:   60,
            visible: $this->shouldShowDivider($campaign),
        );

        /*
        |--------------------------------------------------------------------------
        | HERO HEADLINE
        |--------------------------------------------------------------------------
        */

        $components[] = new HeroHeadline(
            text:      $typography->headline['text']      ?? '',
            x:         (int) ($typography->headline['safe_area']['x'] ?? 70),
            y:         (int) ($typography->headline['safe_area']['y'] ?? 70) + ($badge ? 64 : 8),
            width:     (int) ($typography->headline['safe_area']['width'] ?? 500),
            alignment: $this->resolveAlignment($typography->headline['alignment'] ?? 'left'),
        );

        /*
        |--------------------------------------------------------------------------
        | SUB HEADLINE
        |--------------------------------------------------------------------------
        */

        $subY = (int) ($typography->headline['safe_area']['y'] ?? 70)
            + ($badge ? 64 : 8)
            + 80;

        $components[] = new SubHeadline(
            text:      $this->resolveSubheadlineText($campaign),
            x:         (int) ($typography->headline['safe_area']['x'] ?? 70),
            y:         $subY,
            width:     (int) ($typography->headline['safe_area']['width'] ?? 500),
            alignment: $this->resolveAlignment($typography->headline['alignment'] ?? 'left'),
        );

        /*
        |--------------------------------------------------------------------------
        | JAPANESE VERTICAL TEXT
        | Only for Japanese cuisine campaigns
        |--------------------------------------------------------------------------
        */

        $components[] = new JapaneseVerticalText(
            text:    $this->resolveJapaneseAccentText($campaign),
            x:       980,
            y:       70,
            visible: $this->isJapaneseCuisine($campaign),
        );

        /*
        |--------------------------------------------------------------------------
        | CTA
        |--------------------------------------------------------------------------
        */

        $ctaSafeArea = $typography->headline['safe_area'] ?? [];

        $components[] = new LuxuryCTA(
            text:      $typography->cta['text'] ?? $this->resolveCtaText($campaign),
            x:         (int) ($ctaSafeArea['x'] ?? 70),
            y:         $subY + 48,
            alignment: $this->resolveAlignment($typography->headline['alignment'] ?? 'left'),
        );

        /*
        |--------------------------------------------------------------------------
        | RESERVATION BADGE
        | Only for reservation-oriented campaigns
        |--------------------------------------------------------------------------
        */

        $components[] = new ReservationBadge(
            text:    $this->resolveReservationText($campaign),
            x:       (int) ($ctaSafeArea['x'] ?? 70),
            y:       $subY + 120,
            visible: $this->isReservationCampaign($campaign),
        );

        /*
        |--------------------------------------------------------------------------
        | FILTER INVISIBLE COMPONENTS
        |--------------------------------------------------------------------------
        */

        return collect($components)
            ->filter(fn (ComponentInterface $c) => $c->isVisible())
            ->values()
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER ALL COMPONENTS
    |--------------------------------------------------------------------------
    */

    public function render(array $components, DesignTokens $tokens): array
    {
        return collect($components)
            ->map(fn (ComponentInterface $c) => $c->render($tokens))
            ->values()
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE RESOLVERS
    |--------------------------------------------------------------------------
    */

    private function resolveBadgeText(CampaignPayloadDTO $campaign): ?string
    {
        return match (true) {
            str_contains(strtolower($campaign->campaignType), 'launch') => 'New Menu',
            str_contains(strtolower($campaign->campaignType), 'opening') => 'Grand Opening',
            str_contains(strtolower($campaign->campaignType), 'promo') => 'Special Offer',
            str_contains(strtolower($campaign->campaignType), 'seasonal') => 'Seasonal Special',
            default => null,
        };
    }

    private function resolveSubheadlineText(CampaignPayloadDTO $campaign): string
    {
        if ($campaign->heroItem !== '') {
            return $campaign->heroItem;
        }

        return $campaign->cuisine !== '' ? $campaign->cuisine : '';
    }

    private function resolveCtaText(CampaignPayloadDTO $campaign): string
    {
        return match (true) {
            str_contains(strtolower($campaign->goal), 'reserv') => 'Reserve Your Table',
            str_contains(strtolower($campaign->goal), 'order')  => 'Order Now',
            str_contains(strtolower($campaign->goal), 'visit')  => 'Visit Us Today',
            str_contains(strtolower($campaign->goal), 'launch') => 'Discover the Menu',
            default => 'Learn More',
        };
    }

    private function resolveReservationText(CampaignPayloadDTO $campaign): string
    {
        return 'Reservations Available';
    }

    private function resolveJapaneseAccentText(CampaignPayloadDTO $campaign): string
    {
        return match (true) {
            str_contains(strtolower($campaign->heroItem), 'omakase') => 'おまかせ',
            str_contains(strtolower($campaign->heroItem), 'sushi')   => '寿司',
            str_contains(strtolower($campaign->cuisine), 'japanese') => '日本料理',
            default => '料理',
        };
    }

    private function resolveAlignment(string $alignment): string
    {
        return match (strtolower($alignment)) {
            'center'          => 'center',
            'right'           => 'right',
            'left'            => 'left',
        };
    }

    private function shouldShowDivider(CampaignPayloadDTO $campaign): bool
    {
        $luxuryStyles = ['luxury', 'premium', 'fine dining', 'elegant'];

        foreach ($luxuryStyles as $style) {
            if (str_contains(strtolower($campaign->style), $style)) {
                return true;
            }
        }

        return false;
    }

    private function isJapaneseCuisine(CampaignPayloadDTO $campaign): bool
    {
        return str_contains(strtolower($campaign->cuisine), 'japanese')
            || str_contains(strtolower($campaign->cuisine), 'sushi')
            || str_contains(strtolower($campaign->cuisine), 'omakase');
    }

    private function isReservationCampaign(CampaignPayloadDTO $campaign): bool
    {
        return str_contains(strtolower($campaign->goal), 'reserv')
            || str_contains(strtolower($campaign->ctaStrategy), 'reserv');
    }
}