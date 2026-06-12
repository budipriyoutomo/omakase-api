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

final class LuxuryJapaneseTheme implements ThemeInterface
{
    public function slug(): string
    {
        return 'luxury_japanese';
    }

    public function name(): string
    {
        return 'Luxury Japanese';
    }

    public function keywords(): array
    {
        return [
            'japanese', 'omakase', 'sushi', 'wagyu',
            'luxury', 'premium', 'fine dining', 'kaiseki',
            'minimalist', 'zen', 'elegant', 'refined',
        ];
    }

    public function defaultLayoutMode(): string
    {
        return LayoutMode::AsymmetricLeft->value;
    }

    public function tokens(): DesignTokens
    {
        return new DesignTokens(
            colors: new ColorToken(
                primary:       '#1A1A1A',
                secondary:     '#2C2C2C',
                accent:        '#C9A96E',
                background:    '#0D0D0D',
                surface:       '#1E1E1E',
                textPrimary:   '#F5F0E8',
                textSecondary: '#B8A99A',
                textAccent:    '#C9A96E',
                overlay:       'rgba(0, 0, 0, 0.45)',
            ),
            typography: new TypographyToken(
                headlineFont:         'Cormorant Garamond',
                subheadlineFont:      'Cormorant Garamond',
                bodyFont:             'Zen Kaku Gothic New',
                ctaFont:              'Cormorant Garamond',
                accentFont:           'Noto Serif JP',
                headlineSize:         '52px',
                subheadlineSize:      '22px',
                bodySize:             '14px',
                ctaSize:              '14px',
                headlineWeight:       '300',
                headlineLetterSpacing:'0.1em',
                headlineLineHeight:   '1.15',
            ),
            spacing: new SpacingToken(
                xs:            '4px',
                sm:            '8px',
                md:            '16px',
                lg:            '32px',
                xl:            '56px',
                xxl:           '80px',
                canvasPadding: '70px',
                componentGap:  '20px',
            ),
            shadows: new ShadowToken(
                textShadow:     '0 2px 12px rgba(0,0,0,0.6)',
                headlineShadow: '0 4px 24px rgba(0,0,0,0.5)',
                ctaShadow:      '0 2px 8px rgba(201,169,110,0.3)',
                badgeShadow:    '0 4px 16px rgba(0,0,0,0.4)',
            ),
            gradients: new GradientToken(
                overlayGradient:    'linear-gradient(180deg, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.0) 50%)',
                backgroundGradient: 'linear-gradient(135deg, #1A1A1A 0%, #0D0D0D 100%)',
                ctaGradient:        'linear-gradient(90deg, #C9A96E 0%, #B8935A 100%)',
                badgeGradient:      'linear-gradient(135deg, rgba(201,169,110,0.15) 0%, rgba(201,169,110,0.05) 100%)',
            ),
            button: new ButtonToken(
                borderRadius:  '0px',
                paddingX:      '32px',
                paddingY:      '12px',
                fontSize:      '12px',
                fontWeight:    '400',
                letterSpacing: '0.2em',
                borderWidth:   '1px',
                borderColor:   '#C9A96E',
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