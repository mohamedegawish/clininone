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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('english_name');
            $table->string('phone');
            
            $table->string('ssn')->nullable();
            $table->date('birth_date')->nullable();
            $table->integer('age')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('nationality')->default('Egypt')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('policy_name')->nullable();
            $table->string('class')->nullable();
            $table->string('card_no')->nullable();
            $table->string('status')->default('active')->nullable();

            $table->foreignId('clinic_id')->constrained('clinics')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('clinic_id');
            $table->index('phone');
            $table->index('ssn');
            $table->index('card_no');
            $table->unique(['phone', 'clinic_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
