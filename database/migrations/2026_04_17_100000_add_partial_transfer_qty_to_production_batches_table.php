<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_batches', function (Blueprint $table) {
            if (!Schema::hasColumn('production_batches', 'transferred_s_qty')) {
                $table->unsignedInteger('transferred_s_qty')->default(0)->after('completed_qty');
            }

            if (!Schema::hasColumn('production_batches', 'transferred_m_qty')) {
                $table->unsignedInteger('transferred_m_qty')->default(0)->after('transferred_s_qty');
            }

            if (!Schema::hasColumn('production_batches', 'transferred_l_qty')) {
                $table->unsignedInteger('transferred_l_qty')->default(0)->after('transferred_m_qty');
            }
        });
    }

    public function down(): void
    {
        Schema::table('production_batches', function (Blueprint $table) {
            if (Schema::hasColumn('production_batches', 'transferred_s_qty')) {
                $table->dropColumn('transferred_s_qty');
            }

            if (Schema::hasColumn('production_batches', 'transferred_m_qty')) {
                $table->dropColumn('transferred_m_qty');
            }

            if (Schema::hasColumn('production_batches', 'transferred_l_qty')) {
                $table->dropColumn('transferred_l_qty');
            }
        });
    }
};
