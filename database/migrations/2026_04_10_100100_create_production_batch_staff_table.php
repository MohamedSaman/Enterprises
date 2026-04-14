<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_batch_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_batch_id')->constrained('production_batches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['production_batch_id', 'user_id'], 'pb_staff_unique_batch_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_batch_staff');
    }
};
