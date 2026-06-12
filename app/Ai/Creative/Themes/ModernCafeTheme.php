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

final class ModernCafeTheme implements ThemeInterface
{
    public function slug(): string
    {
        return 'modern_cafe';
    }

    public function name(): string
    {
        return 'Modern Cafe';
    }

    public function keywords(): array
    {
        return [
            'cafe', 'coffee', 'brunch', 'bakery',
            'modern', 'casual', 'hipster', 'artisan',
            'specialty coffee', 'croissant', 'avocado toast',
        ];
    }

    public function defaultLayoutMode(): string
    {
        return LayoutMode::EditorialCenter->value;
    }

    public function tokens(): DesignTokens
    {
        return new DesignTokens(
            colors: new ColorToken(
                primary:       '#2D2016',
                secondary:     '#4A3728',
                accent:        '#E8A857',
                background:    '#FAF6F0',
                surface:       '#F0E8DC',
                textPrimary:   '#2D2016',
                textSecondary: '#7A6555',
                textAccent:    '#C47F2A',
                overlay:       'rgba(45,32,22,0.35)',
            ),
            typography: new TypographyToken(
                headlineFont:         'Playfair Display',
                subheadlineFont:      'Playfair Display',
                bodyFont:             'Inter',
                ctaFont:              'Inter',
                accentFont:           'Playfair Display',
                headlineSize:         '48px',
                subheadlineSize:      '20px',
                bodySize:             '14px',
                ctaSize:              '13px',
                headlineWeight:       '700',
                headlineLetterSpacing:'0.02em',
                headlineLineHeight:   '1.2',
            ),
            spacing: new SpacingToken(
                xs:            '4px',
                sm:            '8px',
                md:            '16px',
                lg:            '28px',
                xl:            '48px',
                xxl:           '72px',
                canvasPadding: '60px',
                componentGap:  '16px',
            ),
            shadows: new ShadowToken(
                textShadow:     '0 1px 8px rgba(45,32,22,0.3)',
                headlineShadow: '0 2px 16px rgba(45,32,22,0.25)',
                ctaShadow:      '0 4px 12px rgba(232,168,87,0.4)',
                badgeShadow:    '0 2px 8px rgba(45,32,22,0.2)',
            ),
            gradients: new GradientToken(
                overlayGradient:    'linear-gradient(180deg, rgba(250,246,240,0.85) 0%, rgba(250,246,240,0.0) 60%)',
                backgroundGradient: 'linear-gradient(135deg, #FAF6F0 0%, #F0E8DC 100%)',
                ctaGradient:        'linear-gradient(90deg, #E8A857 0%, #C47F2A 100%)',
                badgeGradient:      'linear-gradient(135deg, rgba(232,168,87,0.2) 0%, rgba(232,168,87,0.05) 100%)',
            ),
            button: new ButtonToken(
                borderRadius:  '8px',
                paddingX:      '28px',
                paddingY:      '12px',
                fontSize:      '13px',
                fontWeight:    '600',
                letterSpacing: '0.05em',
                borderWidth:   '0px',
                borderColor:   'transparent',
                textTransform: 'none',
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