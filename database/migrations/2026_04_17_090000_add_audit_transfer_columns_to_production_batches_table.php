<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_batches', function (Blueprint $table) {
            if (!Schema::hasColumn('production_batches', 'transferred_to_inventory_at')) {
                $table->timestamp('transferred_to_inventory_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('production_batches', 'transferred_to_inventory_by')) {
                $table->foreignId('transferred_to_inventory_by')
                    ->nullable()
                    ->after('transferred_to_inventory_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('production_batches', function (Blueprint $table) {
            if (Schema::hasColumn('production_batches', 'transferred_to_inventory_by')) {
                $table->dropForeign(['transferred_to_inventory_by']);
                $table->dropColumn('transferred_to_inventory_by');
            }

            if (Schema::hasColumn('production_batches', 'transferred_to_inventory_at')) {
                $table->dropColumn('transferred_to_inventory_at');
            }
        });
    }
};
