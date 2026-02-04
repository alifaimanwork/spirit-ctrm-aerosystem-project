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
        Schema::create('hub_reportdata', function (Blueprint $table) {
            $table->id();
            $table->integer('reportid');
            $table->string('part_name');
            $table->string('part_number');
            $table->string('job_order');
            $table->integer('quality_check');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hub_reportdata');
    }
};
