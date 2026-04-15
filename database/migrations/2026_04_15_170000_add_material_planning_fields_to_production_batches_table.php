<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_batches', function (Blueprint $table) {
            $table->foreignId('production_material_id')
                ->nullable()
                ->after('size')
                ->constrained('production_materials')
                ->nullOnDelete();

            $table->decimal('planned_material_ton', 12, 3)
                ->default(0)
                ->after('target_qty');
        });
    }

    public function down(): void
    {
        Schema::table('production_batches', function (Blueprint $table) {
            $table->dropForeign(['production_material_id']);
            $table->dropColumn(['production_material_id', 'planned_material_ton']);
        });
    }
};
