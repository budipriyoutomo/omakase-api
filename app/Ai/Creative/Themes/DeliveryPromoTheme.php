<?php

declare(strict_types=1);

namespace App\Ai\Creative\Themes;

use App\Ai\Creative\Layouts\LayoutMode;
use App\Ai\Creative\Themes\Contracts\ThemeInterface;
use App\Ai\Creative\Tokens\ButtonToken;
use App\Ai\Creative\Tokens\ColorToken;
use App\Ai\Creative\Tokens\DesignTokens;
use App\Ai\Creative\Tokens\GradientToken;
use App\Ai\Creative\Tokens\ShadowToken;
use App\Ai\Creative\Tokens\SpacingToken;
use App\Ai\Creative\Tokens\TypographyToken;

final class DeliveryPromoTheme implements ThemeInterface
{
    public function slug(): string
    {
        return 'delivery_promo';
    }

    public function name(): string
    {
        return 'Delivery Promo';
    }

    public function keywords(): array
    {
        return [
            'delivery', 'promo', 'discount', 'deal',
            'fast food', 'burger', 'pizza', 'fried chicken',
            'flash sale', 'limited time', 'order now', 'free delivery',
        ];
    }

    public function defaultLayoutMode(): string
    {
        return LayoutMode::BottomWeighted->value;
    }

    public function tokens(): DesignTokens
    {
        return new DesignTokens(
            colors: new ColorToken(
                primary:       '#E63329',
                secondary:     '#FF6B35',
                accent:        '#FFD600',
                background:    '#FFFFFF',
                surface:       '#FFF8F0',
                textPrimary:   '#1A1A1A',
                textSecondary: '#555555',
                textAccent:    '#E63329',
                overlay:       'rgba(230,51,41,0.08)',
            ),
            typography: new TypographyToken(
                headlineFont:         'Montserrat',
                subheadlineFont:      'Montserrat',
                bodyFont:             'Montserrat',
                ctaFont:              'Montserrat',
                accentFont:           'Montserrat',
                headlineSize:         '56px',
                subheadlineSize:      '22px',
                bodySize:             '14px',
                ctaSize:              '16px',
                headlineWeight:       '900',
                headlineLetterSpacing:'-0.02em',
                headlineLineHeight:   '1.05',
            ),
            spacing: new SpacingToken(
                xs:            '4px',
                sm:            '8px',
                md:            '16px',
                lg:            '24px',
                xl:            '40px',
                xxl:           '64px',
                canvasPadding: '48px',
                componentGap:  '12px',
            ),
            shadows: new ShadowToken(
                textShadow:     'none',
                headlineShadow: '0 4px 0px rgba(230,51,41,0.3)',
                ctaShadow:      '0 6px 20px rgba(230,51,41,0.5)',
                badgeShadow:    '0 4px 12px rgba(255,214,0,0.4)',
            ),
            gradients: new GradientToken(
                overlayGradient:    'linear-gradient(0deg, rgba(230,51,41,0.12) 0%, rgba(255,255,255,0) 50%)',
                backgroundGradient: 'linear-gradient(135deg, #FFFFFF 0%, #FFF8F0 100%)',
                ctaGradient:        'linear-gradient(90deg, #E63329 0%, #FF6B35 100%)',
                badgeGradient:      'linear-gradient(135deg, #FFD600 0%, #FFC200 100%)',
            ),
            button: new ButtonToken(
                borderRadius:  '50px',
                paddingX:      '36px',
                paddingY:      '16px',
                fontSize:      '16px',
                fontWeight:    '800',
                letterSpacing: '0.01em',
                borderWidth:   '0px',
                borderColor:   'transparent',
                textTransform: 'uppercase',
            ),
        );
    }

    public function toArray(): array
    {
        return [
            'slug'                => $this->slug(),
            'name'                => $this->name(),
            'keywords'            => $this->keywords(),
            'default_layout_mode' => $this->defaultLayoutMode(),
            'tokens'              => $this->tokens()->toArray(),
        ];
    }
}