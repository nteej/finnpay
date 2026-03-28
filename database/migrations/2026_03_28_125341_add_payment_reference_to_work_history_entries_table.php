<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('work_history_entries', function (Blueprint $table) {
            // Existing manual entries are always public; payment-generated ones start hidden
            $table->boolean('is_public')->default(true)->after('sort_order');
            $table->foreignId('payment_reference_id')->nullable()->constrained('payment_references')->nullOnDelete()->after('is_public');
        });
    }

    public function down(): void
    {
        Schema::table('work_history_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_reference_id');
            $table->dropColumn('is_public');
        });
    }
};
