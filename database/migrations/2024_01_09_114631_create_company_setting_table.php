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
        Schema::dropIfExists('app_company_setting');

        Schema::create('app_company_setting', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value_ar')->nullable();
            $table->text('value_en')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_company_setting');
    }
};
