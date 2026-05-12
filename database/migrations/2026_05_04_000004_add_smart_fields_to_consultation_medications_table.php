<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultation_medications', function (Blueprint $table) {
            $table->foreignId('medication_id')->nullable()->constrained()->nullOnDelete()->after('consultation_id');
            $table->string('dosage')->nullable()->after('name');
            $table->text('instructions')->nullable()->after('duration');
        });
    }

    public function down(): void
    {
        Schema::table('consultation_medications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('medication_id');
            $table->dropColumn(['dosage', 'instructions']);
        });
    }
};
