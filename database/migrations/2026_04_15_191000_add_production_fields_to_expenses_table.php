<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('module')->nullable()->after('expense_type');
            $table->foreignId('production_batch_id')->nullable()->after('module')->constrained('production_batches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('production_batch_id');
            $table->dropColumn('module');
        });
    }
};
