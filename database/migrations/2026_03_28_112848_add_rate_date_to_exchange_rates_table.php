<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            // Drop the old unique that prevents historical rows
            $table->dropUnique(['currency_from', 'currency_to']);

            $table->date('rate_date')->nullable()->after('currency_to');
            $table->decimal('buy_rate', 12, 4)->nullable()->after('rate');
            $table->decimal('sell_rate', 12, 4)->nullable()->after('buy_rate');

            // New unique: one row per currency pair per date
            $table->unique(['currency_from', 'currency_to', 'rate_date']);
        });
    }

    public function down(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->dropUnique(['currency_from', 'currency_to', 'rate_date']);
            $table->dropColumn(['rate_date', 'buy_rate', 'sell_rate']);
            $table->unique(['currency_from', 'currency_to']);
        });
    }
};
