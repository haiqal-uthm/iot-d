<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename the migration to better reflect what it's doing
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Add durian_id field if it doesn't exist
            if (!Schema::hasColumn('inventory_transactions', 'durian_id')) {
                $table->foreignId('durian_id')->nullable()->constrained('durians')->after('farmer_id');
            }
            
            // Add storage_location field if it doesn't exist
            if (!Schema::hasColumn('inventory_transactions', 'storage_location')) {
                $table->string('storage_location')->after('durian_id');
            }
            
            // Make sure other required fields exist
            if (!Schema::hasColumn('inventory_transactions', 'quantity')) {
                $table->decimal('quantity', 10, 2)->after('storage_location'); // Positive for in, negative for out
            }
            
            if (!Schema::hasColumn('inventory_transactions', 'type')) {
                $table->enum('type', ['in', 'out'])->after('quantity');
            }
            
            if (!Schema::hasColumn('inventory_transactions', 'remarks')) {
                $table->string('remarks')->nullable()->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Only drop columns that we added
            $columnsToCheck = ['durian_id', 'storage_location', 'quantity', 'type', 'remarks'];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('inventory_transactions', $column)) {
                    if ($column === 'durian_id') {
                        $table->dropForeign(['durian_id']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
