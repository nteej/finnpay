<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('release_code')->unique();
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('transaction_count')->default(0);
            $table->decimal('total_usd', 10, 2)->default(0);
            $table->decimal('total_eur', 10, 2)->default(0);
            $table->decimal('total_lkr', 10, 2)->default(0);
            $table->decimal('exchange_rate_usd_lkr', 10, 2)->nullable();
            $table->decimal('exchange_rate_eur_lkr', 10, 2)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->enum('status', ['scheduled', 'processing', 'completed', 'failed'])->default('scheduled');
            $table->timestamp('scheduled_at');
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
