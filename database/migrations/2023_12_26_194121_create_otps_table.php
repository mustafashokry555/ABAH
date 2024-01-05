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
        Schema::create('app_patientOtps', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id')->unsigned(); // Assuming each OTP is associated with a user (patient)
            $table->string('otp');
            $table->string('reason');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->foreign('patient_id')->references('PatientId')->on('Patient')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
