<?php

use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | DROP OLD CONSTRAINT
        |--------------------------------------------------------------------------
        */

        DB::statement("
            ALTER TABLE generations
            DROP CONSTRAINT generations_status_check
        ");

        /*
        |--------------------------------------------------------------------------
        | ADD NEW CONSTRAINT
        |--------------------------------------------------------------------------
        */

        DB::statement("
            ALTER TABLE generations
            ADD CONSTRAINT generations_status_check
            CHECK (
                status IN (
                    'pending',
                    'processing',
                    'generated_image',
                    'completed',
                    'failed'
                )
            )
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE generations
            DROP CONSTRAINT generations_status_check
        ");

        DB::statement("
            ALTER TABLE generations
            ADD CONSTRAINT generations_status_check
            CHECK (
                status IN (
                    'pending',
                    'processing',
                    'completed',
                    'failed'
                )
            )
        ");
    }
};