<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('generations', function (Blueprint $table) {
            $table->string('cuisine')->nullable()->after('campaign_type');
            $table->string('audience')->nullable()->after('platform');
            $table->string('goal')->nullable()->after('audience');
            $table->string('mood')->nullable()->after('goal');
            $table->string('hero_item')->nullable()->after('style');
            $table->string('visual_strategy')->nullable()->after('hero_item');
            $table->string('cta_strategy')->nullable()->after('visual_strategy');
            $table->string('aspect_ratio', 20)->nullable()->after('cta_strategy');
        });
    }

    public function down(): void
    {
        Schema::table('generations', function (Blueprint $table) {
            $table->dropColumn([
                'cuisine',
                'audience',
                'goal',
                'mood',
                'hero_item',
                'visual_strategy',
                'cta_strategy',
                'aspect_ratio',
            ]);
        });
    }
};
