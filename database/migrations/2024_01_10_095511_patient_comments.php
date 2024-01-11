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
        Schema::dropIfExists('app_patient_comments');

        Schema::create('app_patient_comments', function (Blueprint $table) {
            $table->id();
            $table->enum('subject', ['complaint', 'gratitude', 'suggestion', 'technical fault']);
            $table->string('name');
            $table->string('mobile');
            $table->text('comment');
            $table->timestamps();
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
