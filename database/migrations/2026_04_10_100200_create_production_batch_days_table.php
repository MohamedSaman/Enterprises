<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_batch_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_batch_id')->constrained('production_batches')->cascadeOnDelete();
            $table->unsignedInteger('day_no');
            $table->date('work_date');
            $table->unsignedInteger('produced_qty')->default(0);
            $table->decimal('expense_amount', 12, 2)->default(0);
            $table->text('expense_note')->nullable();
            $table->json('material_usages')->nullable();
            $table->json('staff_commissions')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['production_batch_id', 'day_no'], 'pb_day_unique_batch_day');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_batch_days');
    }
};
