<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('nama_peminjam');
            $table->string('nis');
            $table->string('judul_buku');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali');
            $table->string('status');
            $table->integer('lama_pinjam');
            $table->boolean('status_perpanjangan')->default(false);
            $table->date('tanggal_pengembalian')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('peminjaman');
    }
}; 