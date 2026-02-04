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
        Schema::create('processed_data', function (Blueprint $table) {
            $table->id();
            
            // Job Card Data
            $table->string('job_card_job_order')->nullable();
            $table->string('job_card_part_number')->nullable();
            $table->timestamp('job_card_timestamp')->nullable();
            
            // SAP Data
            $table->string('sap_job_order')->nullable();
            $table->string('sap_part_number')->nullable();
            $table->timestamp('sap_timestamp')->nullable();
            
            // Actual Part Data (formerly PLC)
            $table->string('actual_part_job_order')->nullable();
            $table->string('actual_part_number')->nullable();
            $table->timestamp('actual_part_timestamp')->nullable();
            
            // Comparison Results
            $table->boolean('job_order_match_jobcard_sap')->default(false);
            $table->boolean('part_number_match_jobcard_sap')->default(false);
            $table->boolean('job_order_match_sap_actual')->default(false);
            $table->boolean('part_number_match_sap_actual')->default(false);
            
            // Status and Metadata
            $table->string('status')->default('pending'); // pending, processing, completed, error
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // For any additional data
            
            $table->timestamps();
            
            // Indexes for faster lookups
            $table->index('job_card_job_order');
            $table->index('sap_job_order');
            $table->index('actual_part_job_order');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processed_data');
    }
};
