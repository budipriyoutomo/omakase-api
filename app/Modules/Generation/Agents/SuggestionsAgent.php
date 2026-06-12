<?php

declare(strict_types=1);

namespace App\Modules\Generation\Agents;

use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;

#[Model('gemini-2.0-flash')]
class SuggestionsAgent implements Agent
{
    use Promptable;

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
        You are a creative marketing strategist specializing in restaurant campaigns.
        Generate concise, actionable campaign ideas.
        Return ONLY a numbered list, one idea per line, no explanation.
        INSTRUCTIONS;
    }
}