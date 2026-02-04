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
        Schema::create('callid_3', function (Blueprint $table) {
            $table->id();
            $table->string('track_identification');
            $table->string('quality_status');
            $table->string('part_number');
            $table->string('job_order');
            $table->string('track');
            $table->string('orientation');
            $table->string('check_port_starboard');
            $table->string('model_check');
            $table->string('output_part_recognition');
            $table->string('anchornut_10nos');
            $table->string('trackstop_3nos_1');
            $table->string('failsafe_4nos_1');
            $table->string('loosehole_3nos');
            $table->string('7mushroom_1rivettail');
            $table->string('cleatangleloosehole_8nos');
            $table->string('cleatangleheighttotrackbase1_1');
            $table->string('cleatangleheighttotrackbase1_2');
            $table->string('side2loosehole_2nos_1');
            $table->string('distancefailsafetotrackbase_1');
            $table->string('side1loosehole_2nos_1');
            $table->string('distancefailsafetotrackbase_2');
            $table->string('failsafeposition_1');
            $table->string('packerside_1');
            $table->string('failsafeposition_2');
            $table->string('gapbetweenpart_1');
            $table->string('gapbetweenpart_2');
            $table->string('gapbetweenpart_3');
            $table->string('side2loosehole_2nos_2');
            $table->string('distancelowerparttotrackbase_1');
            $table->string('side1loosehole_2nos_2');
            $table->string('distancelowerparttotrackbase_2');
            $table->string('failsafeposition_3');
            $table->string('doublerposition_1');
            $table->string('doublerposition_2');
            $table->string('anchornut_8nos');
            $table->string('trackstop_3nos_2');
            $table->string('failsafe_4nos_2');
            $table->string('loosehole_4nos');
            $table->string('6mushroom_1rivettail');
            $table->string('cleatangleloosehole_7nos');            
            $table->string('cleatangleheighttotrackbase2_1');
            $table->string('cleatangleheighttotrackbase2_2');
            $table->string('packerside_2');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('callid_3');
    }
};
