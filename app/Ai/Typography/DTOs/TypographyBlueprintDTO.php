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
                $data['safe_areas'] ?? [],

            layoutStrategy:
                $data['layout_strategy'] ?? '',

            visualReasoning:
                $data['visual_reasoning'] ?? '',
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
        ];
    }
}