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

final class FineDiningTheme implements ThemeInterface
{
    public function slug(): string
    {
        return 'fine_dining';
    }

    public function name(): string
    {
        return 'Fine Dining';
    }

    public function keywords(): array
    {
        return [
            'fine dining', 'tasting menu', 'michelin', 'haute cuisine',
            'french', 'european', 'steak', 'truffle', 'foie gras',
            'sommelier', 'wine pairing', 'reservation', 'anniversary',
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
                primary:       '#1C1814',
                secondary:     '#2E2922',
                accent:        '#9B8B6E',
                background:    '#0F0E0C',
                surface:       '#1C1814',
                textPrimary:   '#EDE8DF',
                textSecondary: '#A09080',
                textAccent:    '#9B8B6E',
                overlay:       'rgba(15,14,12,0.55)',
            ),
            typography: new TypographyToken(
                headlineFont:         'EB Garamond',
                subheadlineFont:      'EB Garamond',
                bodyFont:             'Lato',
                ctaFont:              'Lato',
                accentFont:           'EB Garamond',
                headlineSize:         '50px',
                subheadlineSize:      '20px',
                bodySize:             '13px',
                ctaSize:              '12px',
                headlineWeight:       '400',
                headlineLetterSpacing:'0.06em',
                headlineLineHeight:   '1.2',
            ),
            spacing: new SpacingToken(
                xs:            '4px',
                sm:            '8px',
                md:            '16px',
                lg:            '32px',
                xl:            '60px',
                xxl:           '88px',
                canvasPadding: '80px',
                componentGap:  '24px',
            ),
            shadows: new ShadowToken(
                textShadow:     '0 2px 16px rgba(0,0,0,0.7)',
                headlineShadow: '0 4px 28px rgba(0,0,0,0.6)',
                ctaShadow:      '0 2px 8px rgba(155,139,110,0.25)',
                badgeShadow:    '0 4px 20px rgba(0,0,0,0.5)',
            ),
            gradients: new GradientToken(
                overlayGradient:    'linear-gradient(180deg, rgba(15,14,12,0.6) 0%, rgba(15,14,12,0.0) 55%)',
                backgroundGradient: 'linear-gradient(135deg, #1C1814 0%, #0F0E0C 100%)',
                ctaGradient:        'linear-gradient(90deg, #9B8B6E 0%, #7A6B52 100%)',
                badgeGradient:      'linear-gradient(135deg, rgba(155,139,110,0.12) 0%, rgba(155,139,110,0.04) 100%)',
            ),
            button: new ButtonToken(
                borderRadius:  '0px',
                paddingX:      '40px',
                paddingY:      '14px',
                fontSize:      '11px',
                fontWeight:    '400',
                letterSpacing: '0.25em',
                borderWidth:   '1px',
                borderColor:   '#9B8B6E',
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