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
        Schema::create('harvest_logs', function (Blueprint $table) {
            $table->id();
            $table->string('orchard', 100);
            $table->string('durian_type', 50);
            $table->date('harvest_date');
            $table->integer('total_harvested');
            $table->string('status', 50);
            $table->timestamps();
    
            // Add foreign key constraints if you have orchards and durians tables
            // $table->foreignId('orchard_id')->constrained('orchards');
            // $table->foreignId('durian_id')->constrained('durians');
            
            // Add indexes for filtering
            $table->index('harvest_date');
            $table->index('durian_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harvest_logs');
    }
};
