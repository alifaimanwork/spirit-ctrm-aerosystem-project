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
        // Add hub_data_id to all callid tables
        for ($i = 1; $i <= 4; $i++) {
            $tableName = "callid_{$i}";
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'hub_data_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('hub_data_id')->nullable()->after('id');
                    $table->index('hub_data_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove hub_data_id from all callid tables
        for ($i = 1; $i <= 4; $i++) {
            $tableName = "callid_{$i}";
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'hub_data_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropIndex(['hub_data_id']);
                    $table->dropColumn('hub_data_id');
                });
            }
        }
    }
};
