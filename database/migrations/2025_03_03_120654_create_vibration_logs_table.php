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
        Schema::create('vibration_logs', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->integer('vibration_count');
            $table->tinyInteger('log_type'); // 1 = Harvest, 0 = Vibration
            $table->timestamps(); // auto created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vibration_logs');
    }
};
