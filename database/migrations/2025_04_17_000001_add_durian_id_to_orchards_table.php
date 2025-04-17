<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orchards', function (Blueprint $table) {
            // Add durian relationship
            $table->foreign('durian_id')
                  ->references('id')
                  ->on('durians')
                  ->onDelete('cascade');
            
            // Modify existing device_id to be a foreign key
            $table->unsignedBigInteger('device_id')->change();
            $table->foreign('device_id')
                  ->references('id')
                  ->on('devices')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('orchards', function (Blueprint $table) {
            // Drop durian relationship
            $table->dropForeign(['durian_id']);
            $table->dropColumn('durian_id');
            
            // Drop device relationship
            $table->dropForeign(['device_id']);
        });
    }
};