<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('storage', function (Blueprint $table) {
            $table->id();
            $table->string('storage_location');
            $table->string('durian_type');
            $table->integer('quantity');
            $table->foreignId('harvest_log_id')->constrained('harvest_logs');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('storage');
    }
};