<?php

declare(strict_types=1);

namespace App\Modules\Generation\Agents;

use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;


#[Model('gemini-2.0-flash')]
class ContentGenerationAgent implements Agent
{
    use Promptable;

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
        You are an expert marketing copywriter and campaign strategist.
        Your task is to generate high-quality, conversion-focused marketing content.
        Always be concise, persuasive, and tailored to the specified platform and audience.
        Format your output clearly with sections when appropriate.
        INSTRUCTIONS;
    }
}