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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('farmers')->onDelete('cascade');
            $table->string('storage_location');
            $table->decimal('quantity', 10, 2); // Positive for stock in, negative for stock out
            $table->enum('type', ['in', 'out']);
            $table->string('remarks')->nullable();
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['farmer_id', 'storage_location']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
