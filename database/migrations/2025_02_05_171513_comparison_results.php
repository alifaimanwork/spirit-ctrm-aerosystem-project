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
        Schema::create('comparison_results', function (Blueprint $table) {
            $table->id();
            $table->string('joborder');
            $table->string('partno');
            $table->enum('status', ['PASS', 'FAIL'])->default('PASS');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comparison_results');
    }
};
