<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('generations', function (
            Blueprint $table
        ) {

            /*
            |--------------------------------------------------------------------------
            | AI PROMPTS
            |--------------------------------------------------------------------------
            */

            $table->longText(
                'enhanced_prompt'
            )->nullable()->after('prompt');

            $table->text(
                'negative_prompt'
            )->nullable()->after(
                'enhanced_prompt'
            );

            /*
            |--------------------------------------------------------------------------
            | AI ORCHESTRATION
            |--------------------------------------------------------------------------
            */

            $table->json(
                'orchestration'
            )->nullable()->after(
                'negative_prompt'
            );

            /*
            |--------------------------------------------------------------------------
            | AI AGENT & MODEL
            |--------------------------------------------------------------------------
            */

            $table->string(
                'agent'
            )->nullable()->after(
                'orchestration'
            );

            $table->string(
                'provider'
            )->nullable()->after(
                'agent'
            );

            $table->string(
                'model'
            )->nullable()->after(
                'provider'
            );

            /*
            |--------------------------------------------------------------------------
            | AI GENERATION RESULT
            |--------------------------------------------------------------------------
            */

            $table->text(
                'image_url'
            )->nullable()->after(
                'model'
            );

            $table->json(
                'image_urls'
            )->nullable()->after(
                'image_url'
            );

            /*
            |--------------------------------------------------------------------------
            | AI ANALYTICS
            |--------------------------------------------------------------------------
            */

            $table->integer(
                'tokens_used'
            )->nullable()->after(
                'image_urls'
            );

            $table->decimal(
                'cost',
                10,
                4
            )->nullable()->after(
                'tokens_used'
            );

            $table->integer(
                'generation_time_ms'
            )->nullable()->after(
                'cost'
            );

            /*
            |--------------------------------------------------------------------------
            | AI RAW RESPONSE
            |--------------------------------------------------------------------------
            */

            $table->longText(
                'raw_response'
            )->nullable()->after(
                'generation_time_ms'
            );

            /*
            |--------------------------------------------------------------------------
            | AI EXTRA METADATA
            |--------------------------------------------------------------------------
            */

            $table->json(
                'ai_metadata'
            )->nullable()->after(
                'raw_response'
            );
        });
    }

    public function down(): void
    {
        Schema::table('generations', function (
            Blueprint $table
        ) {

            $table->dropColumn([

                'enhanced_prompt',

                'negative_prompt',

                'orchestration',

                'agent',

                'provider',

                'model',

                'image_url',

                'image_urls',

                'tokens_used',

                'cost',

                'generation_time_ms',

                'raw_response',

                'ai_metadata',
            ]);
        });
    }
};