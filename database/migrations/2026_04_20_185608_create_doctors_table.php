<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('specialty');
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->timestamps();
            $table->index('email');
            $table->index('phone');
            $table->index('status');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
