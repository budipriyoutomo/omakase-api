<?php

namespace App\Ai\Typography\Vision;

use App\Ai\DTOs\CampaignPayloadDTO;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Promptable;

 
#[Model('gemini-2.5-flash')]
class TypographyVisionAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        private readonly CampaignPayloadDTO $dto
    ) {}
    public function instructions(): string
    {
        return <<<INSTRUCTIONS
        You are an elite typography layout intelligence system for restaurant advertising campaigns.

        Analyze the attached restaurant campaign image.

        Your task:
        - identify the best headline placement
        - identify safe CTA placement
        - identify typography-safe negative space
        - identify visual hierarchy
        - identify clean readable zones
        - avoid covering the hero food subject
        - optimize for the specified platform composition

        Campaign context:
        - Goal: {$this->dto->goal}
        - Platform: {$this->dto->platform}
        - Audience: {$this->dto->audience}
        - Brand Style: {$this->dto->style}
        - Mood: {$this->dto->mood}
        - Hero Item: {$this->dto->heroItem}
        - Cuisine: {$this->dto->cuisine}
        - Campaign Type: {$this->dto->campaignType}
        - Visual Strategy: {$this->dto->visualStrategy}
        - CTA Strategy: {$this->dto->ctaStrategy}
        - Aspect Ratio: {$this->dto->aspectRatio}

        CRITICAL: Return ONLY a raw JSON object.
        Do NOT include markdown, code fences, backticks, or any explanation.
        Start your response with { and end with }.
        INSTRUCTIONS;
    }
    public function schema(JsonSchema $schema): array
    {
        return [
            'headline' => $schema->object([
                'text'      => $schema->string()->required(),
                'placement' => $schema->string()->required(),
                'alignment' => $schema->string()->required(),
                'style'     => $schema->string()->required(),
                'safe_area' => $schema->object([
                    'x'      => $schema->integer()->required(),
                    'y'      => $schema->integer()->required(),
                    'width'  => $schema->integer()->required(),
                    'height' => $schema->integer()->required(),
                ])->required(),
            ])->required(),
            'subheadline' => $schema->object([
                'placement' => $schema->string()->required(),
                'style'     => $schema->string()->required(),
            ])->required(),
            'cta' => $schema->object([
                'text'      => $schema->string()->required(),
                'placement' => $schema->string()->required(),
                'style'     => $schema->string()->required(),
            ])->required(),
            'layout_strategy'  => $schema->string()->required(),
            'visual_reasoning' => $schema->string()->required(),
        ];
    }
}