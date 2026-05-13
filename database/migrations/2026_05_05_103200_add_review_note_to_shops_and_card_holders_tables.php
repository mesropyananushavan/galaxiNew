<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->text('review_note')->nullable()->after('is_active');
        });

        Schema::table('card_holders', function (Blueprint $table): void {
            $table->text('review_note')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('card_holders', function (Blueprint $table): void {
            $table->dropColumn('review_note');
        });

        Schema::table('shops', function (Blueprint $table): void {
            $table->dropColumn('review_note');
        });
    }
};
