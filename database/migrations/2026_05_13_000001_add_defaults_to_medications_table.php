<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->string('generic', 255)->nullable()->after('name');
            $table->string('default_dosage', 100)->nullable()->after('generic');
            $table->string('default_frequency', 100)->nullable()->after('default_dosage');
            $table->string('default_route', 100)->nullable()->after('default_frequency');
            $table->string('default_duration', 100)->nullable()->after('default_route');
            $table->text('default_instructions')->nullable()->after('default_duration');
        });
    }

    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->dropColumn(['generic', 'default_dosage', 'default_frequency', 'default_route', 'default_duration', 'default_instructions']);
        });
    }
};
