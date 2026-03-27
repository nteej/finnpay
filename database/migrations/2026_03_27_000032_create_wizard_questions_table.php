<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wizard_questions', function (Blueprint $table) {
            $table->id();
            $table->string('section')->default('General');
            $table->text('question_text');
            $table->string('helper_text')->nullable();
            $table->string('type')->default('text'); // text|textarea|select|radio|checkbox|number|date|boolean
            $table->json('options')->nullable();      // for select/radio/checkbox types
            $table->boolean('is_required')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wizard_questions');
    }
};
