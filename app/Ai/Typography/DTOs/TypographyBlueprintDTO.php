<?php

namespace App\Ai\Typography\DTOs;

class TypographyBlueprintDTO
{
    public function __construct(

        public readonly array $headline,

        public readonly array $subheadline,

        public readonly array $cta,

        public readonly array $safeAreas,

        public readonly string $layoutStrategy,

        public readonly string $visualReasoning,

        // ── Enhanced Typography Fields ───────────────────────────
        /** Decorative text elements (e.g. Japanese vertical text, badges, labels) */
        public readonly array $decorations = [],

        /** Font pairings and custom typography details */
        public readonly array $fontPairing = [],

        /** Visual effects for typography (glow, gradient, stroke, shadow) */
        public readonly array $textEffects = [],

        /** Background treatments for text (pill, underline, highlight bar, mask) */
        public readonly array $textBackgrounds = [],

        /** Responsive breakpoints or scaling rules */
        public readonly array $responsiveRules = [],

    ) {}

    public static function fromArray(
        array $data
    ): self {

        return new self(

            headline:
                $data['headline'] ?? [],

            subheadline:
                $data['subheadline'] ?? [],

            cta:
                $data['cta'] ?? [],

            safeAreas:
                $data['safe_areas'] ?? $data['safeAreas'] ?? [],

            layoutStrategy:
                $data['layout_strategy'] ?? $data['layoutStrategy'] ?? '',

            visualReasoning:
                $data['visual_reasoning'] ?? $data['visualReasoning'] ?? '',

            decorations:
                $data['decorations'] ?? [],

            fontPairing:
                $data['font_pairing'] ?? $data['fontPairing'] ?? [],

            textEffects:
                $data['text_effects'] ?? $data['textEffects'] ?? [],

            textBackgrounds:
                $data['text_backgrounds'] ?? $data['textBackgrounds'] ?? [],

            responsiveRules:
                $data['responsive_rules'] ?? $data['responsiveRules'] ?? [],

        );
    }

    public function toArray(): array
    {
        return [

            'headline' =>
                $this->headline,

            'subheadline' =>
                $this->subheadline,

            'cta' =>
                $this->cta,

            'safe_areas' =>
                $this->safeAreas,

            'layout_strategy' =>
                $this->layoutStrategy,

            'visual_reasoning' =>
                $this->visualReasoning,

            'decorations' =>
                $this->decorations,

            'font_pairing' =>
                $this->fontPairing,

            'text_effects' =>
                $this->textEffects,

            'text_backgrounds' =>
                $this->textBackgrounds,

            'responsive_rules' =>
                $this->responsiveRules,

        ];
    }
}