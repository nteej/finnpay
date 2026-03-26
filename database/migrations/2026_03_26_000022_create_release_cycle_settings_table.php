<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('release_cycle_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('release_day_1')->default(1);   // 1st of month
            $table->unsignedTinyInteger('release_day_2')->default(16);  // 16th of month
            $table->boolean('allow_manual_release')->default(true);
            $table->unsignedInteger('minimum_balance_lkr')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('release_cycle_settings');
    }
};
