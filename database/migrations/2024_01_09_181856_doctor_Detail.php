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
            $table->string('profilePic')->nullable();
            $table->integer('doctor_id')->unsigned();
            $table->string('nationality')->nullable();
            $table->string('nationalityAr')->nullable();
            // $table->string('gender');
            $table->string('experience')->nullable();
            $table->string('experienceAr')->nullable();
            $table->string('lang')->nullable();
            $table->string('langAR')->nullable();
            $table->string('services')->nullable();
            $table->string('servicesAr')->nullable();
            $table->string('qualification')->nullable();
            $table->string('qualificationAr')->nullable();
            $table->string('membership')->nullable();
            $table->string('membershipAr')->nullable();
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
