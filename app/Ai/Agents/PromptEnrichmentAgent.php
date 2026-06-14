<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;

#[Model('gemini-2.0-flash')]
class PromptEnrichmentAgent implements Agent
{
    use Promptable;

    public function __construct(
        private readonly string $heroItem,
        private readonly string $cuisineType,
        private readonly string $campaignType,
        private readonly string $visualStyle,
        private readonly string $mood,
    ) {}

    public function prompt(): string
    {
        return <<<PROMPT
You are an expert food photography director specializing in restaurant marketing for Indonesian F&B businesses.

A restaurant owner wants to generate an Instagram marketing image. The menu item is not in our knowledge base, so you must provide professional food photography enrichment.

## Input context:
- Menu item: {$this->heroItem}
- Cuisine type: {$this->cuisineType}
- Campaign type: {$this->campaignType}
- Visual style: {$this->visualStyle}
- Mood: {$this->mood}

## Task:
Provide professional food photography direction for this specific menu item. Think like a food photography director who shoots for Michelin-starred restaurants and top Indonesian F&B brands.

## Output — respond ONLY with valid JSON, no markdown, no explanation:
{
  "food_category": "inferred category name in English",
  "visual_keywords": ["keyword1", "keyword2", "keyword3"],
  "texture_descriptors": ["texture1", "texture2"],
  "lighting_preset": "specific lighting description",
  "camera_angle": "specific angle recommendation",
  "plating_style": "plating and prop description",
  "color_palette": ["color1", "color2", "color3"],
  "avoid_elements": ["avoid1", "avoid2", "avoid3"],
  "cuisine_props": ["prop1", "prop2"],
  "campaign_mood": ["mood1", "mood2"]
}

All values must be specific, professional, and optimized for Flux image generation. No generic descriptions.
PROMPT;
    }
}