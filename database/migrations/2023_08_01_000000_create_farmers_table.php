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
        Schema::create('farmers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ic_number')->nullable(); // ID card number
            $table->string('farm_name')->nullable();
            $table->string('address')->nullable();
            $table->string('profile_image')->nullable();
            $table->text('notes')->nullable(); // Notes or remarks
            $table->json('assigned_orchards')->nullable(); // Store orchard IDs as JSON
            $table->timestamps();
            
            // Add foreign key constraint separately to ensure users table exists
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmers');
    }
};