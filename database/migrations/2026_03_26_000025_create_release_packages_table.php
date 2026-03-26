<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('release_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('color', 20)->default('slate');   // slate | indigo | amber
            $table->tinyInteger('releases_per_month')->default(2);
            $table->tinyInteger('release_day_1')->default(1);
            $table->tinyInteger('release_day_2')->nullable();  // null if releases_per_month = 1
            $table->unsignedInteger('minimum_balance_lkr')->default(0);
            $table->boolean('allow_manual_release')->default(false);
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('release_packages');
    }
};
