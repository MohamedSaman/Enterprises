<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_code')->unique();
            $table->enum('size', ['S', 'M', 'L']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedInteger('target_qty')->default(0);
            $table->unsignedInteger('completed_qty')->default(0);
            $table->foreignId('supervisor_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->enum('status', ['active', 'completed'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_batches');
    }
};
