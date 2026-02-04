<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sap_data', function (Blueprint $table) {
            $table->id();
            $table->string('joborder');
            $table->string('partno');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sap_data');
    }
};
