<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('clinic_id')->nullable()->after('role');
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('set null');
            $table->index('clinic_id');
        });

        // Back-fill: for every existing doctor user, set clinic_id from clinic_doctor pivot
        \DB::statement("
            UPDATE users u
            JOIN doctors d ON d.user_id = u.id
            JOIN clinic_doctor cd ON cd.doctor_id = d.id
            SET u.clinic_id = cd.clinic_id
            WHERE u.clinic_id IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropIndex(['clinic_id']);
            $table->dropColumn('clinic_id');
        });
    }
};
