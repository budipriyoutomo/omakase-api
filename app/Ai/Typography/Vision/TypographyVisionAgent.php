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
You are an elite typography & layout intelligence system for restaurant advertising campaigns. Your expertise is creating visually stunning, magazine-quality typography that elevates the campaign image.

Analyze the attached restaurant campaign image and determine the most aesthetically powerful typography layout.

=== CAMPAIGN CONTEXT ===
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

=== TYPOGRAPHY PRINCIPLES ===

For HEADLINE:
1. Use large, bold type that commands attention — think editorial covers
2. For luxury: serif fonts, wide letter-spacing, uppercase, elegant
3. For energetic: bold sans-serif, dynamic angles, layered text
4. Position where it frames the food, not covers it
5. Consider partial text behind the subject for depth
6. Use text-shadow/glow for readability on busy backgrounds
7. Line-break strategically for visual rhythm

For SUBHEADLINE:
1. Smaller, supporting role — create contrast with headline
2. Use italic or light weight for elegance
3. Can overlap or sit inside the headline for modern look
4. Consider vertical text for Japanese/Asian themes

For CTA (Call to Action):
1. Make it impossible to miss — contrast is key
2. Pill-shaped or underline style buttons
3. Position at natural eye-scan endpoint (bottom-third usually)
4. Use gradient or solid background for legibility
5. Can be positioned diagonally for energy

For DECORATIONS:
1. Vertical Japanese text for Asian cuisine campaigns
2. Price callouts, badges ("NEW", "LIMITED", "PREMIUM")
3. Decorative lines, dividers, or geometric accents
4. Percentage-off badges for promo campaigns
5. Location/address in elegant small type

For TEXT EFFECTS (choose based on mood):
- luxury glow: subtle white glow on dark backgrounds
- gradient text: gold-to-white gradient for premium feel
- text stroke: thin outline for modern look
- drop shadow: deep shadow for readability
- glassmorphism: frosted glass background behind text

For BACKGROUND TREATMENTS:
- text-background pill: rounded pill behind text
- highlight bar: colored bar/line behind text
- gradient overlay: smooth gradient behind text block
- dark overlay strip: semi-transparent dark bar

=== OUTPUT RULES ===
- safe_area coordinates: 0-1000 scale (percentage of canvas width/height)
- headline safe_area must NOT overlap the hero food subject
- Choose font_pairing wisely based on cuisine/campaign:
  * Japanese Luxury: "Cormorant Garamond" + "Inter"
  * Modern Cafe: "Playfair Display" + "Inter"
  * Promo/Delivery: "Bebas Neue" + "Inter"
  * Fine Dining: "Bodoni Moda" + "Neue Haas Grotesk"
- visual_reasoning should explain WHY this layout works
- Keep text short and powerful — less is more
- whitespace is a design element, use it

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
                'font_size' => $schema->string(),
                'font_weight' => $schema->string(),
                'letter_spacing' => $schema->string(),
                'safe_area' => $schema->object([
                    'x'      => $schema->integer()->required(),
                    'y'      => $schema->integer()->required(),
                    'width'  => $schema->integer()->required(),
                    'height' => $schema->integer()->required(),
                ])->required(),
            ])->required(),
            'subheadline' => $schema->object([
                'text'      => $schema->string(),
                'placement' => $schema->string()->required(),
                'style'     => $schema->string()->required(),
                'font_size' => $schema->string(),
            ]),
            'cta' => $schema->object([
                'text'      => $schema->string()->required(),
                'placement' => $schema->string()->required(),
                'style'     => $schema->string()->required(),
                'background_style' => $schema->string(),
                'border_radius'    => $schema->string(),
            ])->required(),
            'decorations' => $schema->array(
                $schema->object([
                    'type'      => $schema->string()->required(),
                    'text'      => $schema->string(),
                    'placement' => $schema->string()->required(),
                    'style'     => $schema->string(),
                    'font_size' => $schema->string(),
                ])
            ),
            'font_pairing' => $schema->object([
                'headline'  => $schema->string(),
                'body'      => $schema->string(),
                'accent'    => $schema->string(),
                'reasoning' => $schema->string(),
            ]),
            'text_effects' => $schema->array(
                $schema->object([
                    'element' => $schema->string()->required(),
                    'effect'  => $schema->string()->required(),
                    'value'   => $schema->string(),
                ])
            ),
            'text_backgrounds' => $schema->array(
                $schema->object([
                    'element'    => $schema->string()->required(),
                    'type'       => $schema->string()->required(),
                    'color'      => $schema->string(),
                    'opacity'    => $schema->string(),
                    'padding'    => $schema->string(),
                    'border_radius' => $schema->string(),
                ])
            ),
            'layout_strategy'  => $schema->string()->required(),
            'visual_reasoning' => $schema->string()->required(),
            'responsive_rules' => $schema->array(
                $schema->object([
                    'breakpoint' => $schema->string(),
                    'headline_size' => $schema->string(),
                    'cta_size'      => $schema->string(),
                ])
            ),
        ];
    }
}
