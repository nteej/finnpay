<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_reference_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('release_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payer_name');
            $table->string('payer_email')->nullable();
            $table->string('currency_type', 3)->default('USD');
            $table->decimal('amount_usd', 10, 2)->nullable();
            $table->decimal('amount_eur', 10, 2)->nullable();
            $table->decimal('fee_usd', 10, 2)->default(0);
            $table->decimal('fee_eur', 10, 2)->default(0);
            $table->decimal('final_usd', 10, 2)->nullable();
            $table->decimal('final_eur', 10, 2)->nullable();
            $table->decimal('final_lkr', 10, 2)->nullable();
            $table->decimal('cv_rate', 8, 4)->nullable();
            $table->decimal('lkr_rate', 10, 2)->nullable();
            $table->string('paypal_transaction_id')->nullable();
            $table->enum('status', ['pending', 'cleared', 'released'])->default('cleared');
            $table->date('received_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
