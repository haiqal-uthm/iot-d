<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('harvest_logs', function (Blueprint $table) {
            $table->decimal('estimated_weight', 8, 2)->nullable();
            $table->json('grade')->nullable();
            $table->json('condition')->nullable();
            $table->json('storage_location')->nullable();
            $table->text('remarks')->nullable();
            $table->text('harvester_signature')->nullable();
        });
    }

    public function down()
    {
        Schema::table('harvest_logs', function (Blueprint $table) {
            $table->dropColumn([
                'estimated_weight',
                'grade',
                'condition',
                'storage_location',
                'remarks',
                'harvester_signature'
            ]);
        });
    }
};