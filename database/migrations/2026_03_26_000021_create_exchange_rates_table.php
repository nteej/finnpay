<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency_from', 3); // USD, EUR
            $table->string('currency_to', 3);   // LKR
            $table->decimal('rate', 12, 4);      // e.g. 295.0000
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['currency_from', 'currency_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
