<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_batch_days', function (Blueprint $table) {
            $table->unsignedInteger('produced_s_qty')->default(0)->after('produced_qty');
            $table->unsignedInteger('produced_m_qty')->default(0)->after('produced_s_qty');
            $table->unsignedInteger('produced_l_qty')->default(0)->after('produced_m_qty');
        });
    }

    public function down(): void
    {
        Schema::table('production_batch_days', function (Blueprint $table) {
            $table->dropColumn(['produced_s_qty', 'produced_m_qty', 'produced_l_qty']);
        });
    }
};
