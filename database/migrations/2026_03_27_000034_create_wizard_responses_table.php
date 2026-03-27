<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wizard_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('onboarding_wizard_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wizard_question_id')->constrained()->cascadeOnDelete();
            $table->text('answer')->nullable();
            $table->unique(['onboarding_wizard_id', 'wizard_question_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wizard_responses');
    }
};
