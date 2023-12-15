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
        Schema::create('app_rate_doctors', function (Blueprint $table) {
            $table->id();
            $table->integer('doctor_id')->unsigned();
            $table->integer('patient_id')->unsigned();
            $table->integer('rate');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->foreign('doctor_id')->references('EmpID')->on('Employee_Mst')->onDelete('cascade');
            $table->foreign('patient_id')->references('PatientId')->on('Patient')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
