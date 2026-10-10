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
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no')->unique();       // Employee No / NIK
            $table->string('name');                      // Name
            $table->string('employment_under')->nullable(); // Employment Under
            $table->string('departemen')->nullable();      // Dept
            $table->string('indonesia_level')->nullable();  // Indonesia Level
            $table->string('location')->nullable();         // Location
            $table->string('schedule')->nullable();         // Schedule / Shift
            $table->string('no_hp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};