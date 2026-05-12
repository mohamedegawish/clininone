<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // appointments: source is filtered in NotificationController every 30s
        Schema::table('appointments', function (Blueprint $table) {
            $table->index('source', 'idx_appointments_source');
            $table->index('is_paid', 'idx_appointments_is_paid');
            $table->index('queue_number', 'idx_appointments_queue_number');
            // Composite covering index for the clinic dashboard query pattern
            $table->index(['clinic_id', 'status', 'appointment_date'], 'idx_appointments_clinic_status_date');
        });

        // patients: status is filtered for active/inactive summary
        Schema::table('patients', function (Blueprint $table) {
            $table->index(['clinic_id', 'status'], 'idx_patients_clinic_status');
        });

        // doctors: specialty grouped in admin dashboard
        Schema::table('doctors', function (Blueprint $table) {
            $table->index('specialty', 'idx_doctors_specialty');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('idx_appointments_source');
            $table->dropIndex('idx_appointments_is_paid');
            $table->dropIndex('idx_appointments_queue_number');
            $table->dropIndex('idx_appointments_clinic_status_date');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('idx_patients_clinic_status');
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->dropIndex('idx_doctors_specialty');
        });
    }
};
