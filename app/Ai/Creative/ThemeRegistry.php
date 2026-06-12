<?php

declare(strict_types=1);

namespace App\Ai\Creative;

use App\Ai\Creative\Themes\Contracts\ThemeInterface;
use App\Ai\Creative\Themes\DeliveryPromoTheme;
use App\Ai\Creative\Themes\FineDiningTheme;
use App\Ai\Creative\Themes\LuxuryJapaneseTheme;
use App\Ai\Creative\Themes\ModernCafeTheme;
use InvalidArgumentException;

final class ThemeRegistry
{
    /**
     * @var array<string, ThemeInterface>
     */
    private array $themes = [];

    public function __construct()
    {
        $this->register(new LuxuryJapaneseTheme);
        $this->register(new ModernCafeTheme);
        $this->register(new DeliveryPromoTheme);
        $this->register(new FineDiningTheme);
    }

    public function register(ThemeInterface $theme): void
    {
        $this->themes[$theme->slug()] = $theme;
    }

    public function resolve(string $slug): ThemeInterface
    {
        if (! isset($this->themes[$slug])) {
            throw new InvalidArgumentException(
                "Theme [{$slug}] is not registered. Available themes: "
                . implode(', ', $this->slugs())
            );
        }

        return $this->themes[$slug];
    }

    public function has(string $slug): bool
    {
        return isset($this->themes[$slug]);
    }

    public function all(): array
    {
        return $this->themes;
    }

    public function slugs(): array
    {
        return array_keys($this->themes);
    }

    public function fallback(): ThemeInterface
    {
        return $this->themes['fine_dining'];
    }
}