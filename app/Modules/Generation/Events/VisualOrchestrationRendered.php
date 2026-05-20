<?php

declare(strict_types=1);

namespace App\Modules\Generation\Events;

use App\Models\Generation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class VisualOrchestrationRendered
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Generation $generation,
        public readonly array $orchestration,
    ) {}
}
