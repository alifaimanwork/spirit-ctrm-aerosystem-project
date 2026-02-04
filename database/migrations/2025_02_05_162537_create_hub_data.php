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
        Schema::create('hub_data', function (Blueprint $table) {
            $table->id();
            $table->string('joborder');
            $table->string('partno');
            $table->string('all_str', 1000); // Increased length to handle large data
            $table->boolean('is_processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hub_data');
    }
};
