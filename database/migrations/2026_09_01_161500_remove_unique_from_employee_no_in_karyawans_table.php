<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Hapus unique constraint pada kolom employee_no
            $table->dropUnique(['employee_no']);
        });
    }

    public function down()
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->unique('employee_no');
        });
    }
};
