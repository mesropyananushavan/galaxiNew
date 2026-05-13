<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('card_types', function (Blueprint $table): void {
            $table->text('rollout_note')->nullable()->after('activation_note');
        });
    }

    public function down(): void
    {
        Schema::table('card_types', function (Blueprint $table): void {
            $table->dropColumn('rollout_note');
        });
    }
};
