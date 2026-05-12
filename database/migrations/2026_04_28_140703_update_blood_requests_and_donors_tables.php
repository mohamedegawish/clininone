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
        Schema::table('blood_requests', function (Blueprint $table) {
            // Update status column to support new statuses
            // We use change() but since we are in Laravel 12, we can just redefine it
            $table->string('status')->default('new')->change(); // new, contacting, completed, cancelled
            $table->string('city')->nullable()->after('governorate');
            $table->text('address')->nullable()->after('city');
        });

        Schema::table('donors', function (Blueprint $table) {
            $table->string('status')->default('active')->after('governorate'); // active, inactive
            $table->string('city')->nullable()->after('governorate');
            $table->text('address')->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            $table->dropColumn(['city', 'address']);
        });

        Schema::table('donors', function (Blueprint $table) {
            $table->dropColumn(['status', 'city', 'address']);
        });
    }
};
