<?php

declare(strict_types=1);

namespace App\Ai\Renderers\Support;

final class CssPropertyBuilder
{
    private array $properties = [];

    public function add(string $property, string $value): self
    {
        $this->properties[$property] = $value;

        return $this;
    }

    public function addMany(array $styles): self
    {
        foreach ($styles as $property => $value) {
            $this->add(
                $this->toKebabCase($property),
                (string) $value
            );
        }

        return $this;
    }

    public function build(): string
    {
        return collect($this->properties)
            ->map(fn ($value, $property) => "{$property}: {$value}")
            ->implode('; ');
    }

    public static function fromArray(array $styles): string
    {
        return (new self)->addMany($styles)->build();
    }

    private function toKebabCase(string $property): string
    {
        // Convert snake_case to kebab-case
        return str_replace('_', '-', $property);
    }
}