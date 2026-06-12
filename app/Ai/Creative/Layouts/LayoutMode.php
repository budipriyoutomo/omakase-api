<?php

declare(strict_types=1);

namespace App\Ai\Creative\Layouts;

enum LayoutMode: string
{
    case EditorialCenter  = 'editorial_center';
    case AsymmetricLeft   = 'asymmetric_left';
    case AsymmetricRight  = 'asymmetric_right';
    case TopWeighted      = 'top_weighted';
    case BottomWeighted   = 'bottom_weighted';
    case FullOverlay      = 'full_overlay';
    case MinimalFloat     = 'minimal_float';

    public function label(): string
    {
        return match($this) {
            self::EditorialCenter  => 'Editorial Center',
            self::AsymmetricLeft   => 'Asymmetric Left',
            self::AsymmetricRight  => 'Asymmetric Right',
            self::TopWeighted      => 'Top Weighted',
            self::BottomWeighted   => 'Bottom Weighted',
            self::FullOverlay      => 'Full Overlay',
            self::MinimalFloat     => 'Minimal Float',
        };
    }
}