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
        Schema::create('production_material_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_material_id')->constrained('production_materials')->onDelete('cascade');
            $table->string('batch_no'); 
            $table->string('size')->nullable(); // Changed from enum to string for debugging
            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('remaining_quantity', 15, 2)->default(0);
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->timestamps();

            $table->index(['production_material_id', 'batch_no'], 'pm_id_batch_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_material_batches');
    }
};
