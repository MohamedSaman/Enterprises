<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_batches', function (Blueprint $table) {
            $table->foreignId('production_material_batch_id')
                ->nullable()
                ->after('production_material_id')
                ->constrained('production_material_batches')
                ->nullOnDelete();

            $table->index(['production_material_id', 'size', 'production_material_batch_id'], 'pb_material_size_batch_index');
        });
    }

    public function down(): void
    {
        Schema::table('production_batches', function (Blueprint $table) {
            $table->dropForeign(['production_material_batch_id']);
            $table->dropIndex('pb_material_size_batch_index');
            $table->dropColumn('production_material_batch_id');
        });
    }
};
