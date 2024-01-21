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
        Schema::dropIfExists('app_doctor_details');


        Schema::create('app_doctor_details', function (Blueprint $table) {
            $table->id();
            $table->string('profilePic');
            $table->integer('doctor_id')->unsigned();
            $table->string('nationality');
            $table->string('nationalityAr');
            // $table->string('gender');
            $table->string('experience');
            $table->string('experienceAr');
            $table->string('lang');
            $table->string('langAR');
            $table->string('services');
            $table->string('servicesAr');
            $table->string('qualification');
            $table->string('qualificationAr');
            $table->string('membership');
            $table->string('membershipAr');
            // $table->string('speciality');
            $table->timestamps();

            $table->foreign('doctor_id')->references('EmpID')->on('Employee_Mst')->onDelete('cascade');
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