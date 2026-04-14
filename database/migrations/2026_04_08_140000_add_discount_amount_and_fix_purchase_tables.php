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
        // Add discount_amount to purchase_orders if it doesn't exist
        if (!Schema::hasColumn('purchase_orders', 'discount_amount')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->decimal('discount_amount', 15, 2)->default(0)->after('due_amount');
            });
        }

        // Change quantity from integer to decimal in purchase_order_items
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->decimal('quantity', 15, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('purchase_orders', 'discount_amount')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->dropColumn('discount_amount');
            });
        }

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
    }
};
