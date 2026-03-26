<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('freelancer_id')->unique()->nullable()->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->string('local_currency', 3)->default('LKR');
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'freelancer_id', 'phone', 'bank_name', 'bank_branch',
                'bank_account_number', 'bank_account_holder', 'local_currency', 'is_active',
            ]);
        });
    }
};
