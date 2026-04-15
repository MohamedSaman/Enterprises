<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_batch_days', function (Blueprint $table) {
            $table->json('expense_items')->nullable()->after('expense_amount');
        });
    }

    public function down(): void
    {
        Schema::table('production_batch_days', function (Blueprint $table) {
            $table->dropColumn('expense_items');
        });
    }
};
