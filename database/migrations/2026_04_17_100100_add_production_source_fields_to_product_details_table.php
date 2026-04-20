<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_details', function (Blueprint $table) {
            if (!Schema::hasColumn('product_details', 'source_production_batch_id')) {
                $table->unsignedBigInteger('source_production_batch_id')->nullable()->after('supplier_id');
                $table->index(['source_production_batch_id', 'name'], 'pd_source_batch_name_idx');
            }

            if (!Schema::hasColumn('product_details', 'production_size')) {
                $table->enum('production_size', ['S', 'M', 'L'])->nullable()->after('source_production_batch_id');
                $table->index(['source_production_batch_id', 'production_size'], 'pd_source_batch_size_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_details', function (Blueprint $table) {
            if (Schema::hasColumn('product_details', 'production_size')) {
                $table->dropIndex('pd_source_batch_size_idx');
                $table->dropColumn('production_size');
            }

            if (Schema::hasColumn('product_details', 'source_production_batch_id')) {
                $table->dropIndex('pd_source_batch_name_idx');
                $table->dropColumn('source_production_batch_id');
            }
        });
    }
};
