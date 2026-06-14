<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique();
            $table->string('name');
            $table->json('aliases');
            $table->json('visual_keywords');
            $table->json('texture_descriptors');
            $table->string('lighting_preset')->nullable();
            $table->string('camera_angle')->nullable();
            $table->text('plating_style')->nullable();
            $table->json('color_palette');
            $table->json('avoid');
            $table->json('cuisine_affinity');
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_categories');
    }
};