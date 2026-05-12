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
        Schema::table('doctors', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female'])->after('status')->nullable();
            $table->integer('experience_years')->after('gender')->nullable();
            $table->string('qualification')->after('experience_years')->nullable();
            $table->text('bio')->after('qualification')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['gender', 'experience_years', 'qualification', 'bio']);
        });
    }
};
