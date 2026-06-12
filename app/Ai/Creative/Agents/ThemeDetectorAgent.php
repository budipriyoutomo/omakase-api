<?php

declare(strict_types=1);

namespace App\Ai\Creative\Agents;

use App\Ai\Creative\ThemeRegistry;
use App\Ai\DTOs\CampaignPayloadDTO;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;

#[Model('gemini-2.5-flash')]
class ThemeDetectorAgent implements Agent
{
    use Promptable;

    public function __construct(
        private readonly CampaignPayloadDTO $dto,
        private readonly ThemeRegistry $registry,
    ) {}

    public function instructions(): string
    {
        $availableThemes = collect($this->registry->all())
            ->map(fn ($theme) => sprintf(
                '- "%s": keywords: %s',
                $theme->slug(),
                implode(', ', $theme->keywords())
            ))
            ->implode("\n");

        return <<<INSTRUCTIONS
        You are a restaurant campaign theme classifier.

        Your job is to select the most fitting visual theme
        for a restaurant campaign based on the campaign context.

        Available themes:
        {$availableThemes}

        Campaign context:
        - Cuisine: {$this->dto->cuisine}
        - Style: {$this->dto->style}
        - Mood: {$this->dto->mood}
        - Goal: {$this->dto->goal}
        - Campaign Type: {$this->dto->campaignType}
        - Audience: {$this->dto->audience}
        - Hero Item: {$this->dto->heroItem}

        Rules:
        - Return ONLY the theme slug string. Nothing else.
        - Must be one of the available theme slugs exactly.
        - No explanation, no JSON, no markdown.
        INSTRUCTIONS;
    }
}
