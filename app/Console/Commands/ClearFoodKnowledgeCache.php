<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AI\FoodKnowledgeService;
use Illuminate\Console\Command;

class ClearFoodKnowledgeCache extends Command
{
    protected $signature = 'kb:clear';
    protected $description = 'Clear food knowledge base cache';

    public function __construct(
        private readonly FoodKnowledgeService $service
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->service->clearCache();
        $this->info('Food knowledge base cache cleared.');
        $this->info('Cache will be rebuilt on next request.');
    }
}