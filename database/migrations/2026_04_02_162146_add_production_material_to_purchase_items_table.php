<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            $table->foreignId('production_material_id')->nullable()->constrained('production_materials')->onDelete('cascade');
            $table->string('size')->nullable()->after('production_material_id');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->enum('order_type', ['inventory', 'production'])->default('inventory')->after('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            //
        });
    }
};
