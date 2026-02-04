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
        Schema::table('hub_data', function (Blueprint $table) {
            $table->boolean('is_compared')->default(false)->after('is_processed');
            $table->timestamp('compared_at')->nullable()->after('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hub_data', function (Blueprint $table) {
            $table->dropColumn(['is_compared', 'compared_at']);
        });
    }
};
