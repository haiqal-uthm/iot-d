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
        Schema::create('storage', function (Blueprint $table) {
            $table->id();
            $table->string('storage', 100);
            $table->string('durian_type', 50);
            $table->integer('quantity');
            $table->string('status', 50);
            $table->timestamps();
            
            // Add index for frequently searched columns
            $table->index('durian_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage');
    }
};
