<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freelancer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('bio')->nullable();
            $table->string('skills')->nullable();           // comma-separated
            $table->unsignedSmallInteger('hourly_rate')->nullable();
            $table->string('hourly_rate_currency', 3)->default('USD');
            $table->string('availability')->default('open'); // open | part_time | unavailable
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->string('category')->nullable();
            $table->string('username')->unique()->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freelancer_profiles');
    }
};
