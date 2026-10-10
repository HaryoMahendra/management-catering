<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable(); // Siapa yang melakukan aksi
        $table->string('activity'); // Jenis aktivitas (Contoh: Login, Tambah Data, Edit, Hapus)
        $table->text('description'); // Deskripsi detail (Contoh: Mengubah data user John Doe)
        $table->string('ip_address')->nullable(); // Alamat IP user
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('activity_logs');
}
};
