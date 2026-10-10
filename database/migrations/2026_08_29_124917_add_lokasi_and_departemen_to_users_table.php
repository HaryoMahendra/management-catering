<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek jika kolom departemen belum ada, baru tambahkan
            if (!Schema::hasColumn('users', 'departemen')) {
                $table->text('departemen')->nullable()->after('lokasi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'departemen')) {
                $table->dropColumn('departemen');
            }
        });
    }
};