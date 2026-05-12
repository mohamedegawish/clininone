<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            // Vital Signs
            $table->string('bp', 20)->nullable()->after('notes');         // Blood Pressure e.g. 120/80
            $table->string('temp', 10)->nullable()->after('bp');          // Temperature
            $table->string('pulse', 10)->nullable()->after('temp');       // Pulse rate
            $table->string('hr', 10)->nullable()->after('pulse');         // Heart Rate
            $table->string('rr', 10)->nullable()->after('hr');            // Respiratory Rate
            $table->string('spo2', 10)->nullable()->after('rr');          // Oxygen Saturation
            $table->string('weight', 10)->nullable()->after('spo2');      // Weight
            $table->string('height', 10)->nullable()->after('weight');    // Height

            // Medications stored as JSON array
            $table->json('medications')->nullable()->after('height');
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn(['bp', 'temp', 'pulse', 'hr', 'rr', 'spo2', 'weight', 'height', 'medications']);
        });
    }
};
