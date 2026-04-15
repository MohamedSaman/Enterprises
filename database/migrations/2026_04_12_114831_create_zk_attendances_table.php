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
        Schema::create('zk_attendances', function (Blueprint $table) {
            $table->id();
            $table->string('emp_code');
            $table->dateTime('punch_time');
            $table->string('punch_state')->nullable()->comment('0=Check-in, 1=Check-out, 2=Break-out, 3=Break-in, 4=OT-in, 5=OT-out');
            $table->integer('verify_type')->nullable()->comment('1=Fingerprint, 2=Card, 3=Password, 4=Face, 15=Palm');
            $table->string('terminal_sn')->nullable()->comment('Device serial number');
            $table->string('terminal_alias')->nullable()->comment('Device alias name');
            $table->string('area_alias')->nullable()->comment('Area alias');
            $table->decimal('temperature', 4, 1)->nullable()->comment('Body temperature reading');
            $table->integer('is_mask')->nullable()->comment('null=not checked, 0=no mask, 1=mask, 255=disabled');
            $table->dateTime('upload_time')->nullable()->comment('When data was uploaded to server');
            $table->timestamps();

            $table->unique(['emp_code', 'punch_time'], 'zk_att_emp_punch_unique');
            $table->index('emp_code', 'zk_att_emp_code_index');
            $table->index('punch_time', 'zk_att_punch_time_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zk_attendances');
    }
};
