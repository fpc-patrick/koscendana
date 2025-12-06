<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // tabel utama penghuni
        Schema::create('penghunis', function (Blueprint $table) {
            $table->id('idpenghuni'); // primary key auto increment
            $table->string('kode_penghuni')->unique(); // contoh: P001, P002
            $table->string('nama');
            $table->string('nohp');
            $table->string('nohp_orangtua')->nullable();
            $table->text('alamat')->nullable();
            $table->string('fotoktp')->nullable(); // path file gambar
             $table->enum('status_penghuni', ['Aktif','Nonaktif'])->default('Nonaktif');
            // penting untuk relasi withTimestamps()
            $table->timestamps();

        });

        // pivot: hubungan penghuni dengan kamar
        Schema::create('penghuni_kamar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penghuni_id'); // FK ke penghunis
            $table->string('nokamar'); // FK ke kamars.nokamar
            $table->date('tgl_masuk')->nullable();
            $table->date('tgl_keluar')->nullable();
            $table->string('penyewa_nama')->nullable();
            $table->string('penyewa_nohp')->nullable();
           
            $table->timestamps();

            $table->foreign('penghuni_id')
                  ->references('idpenghuni')->on('penghunis')
                  ->onDelete('cascade');

            $table->foreign('nokamar')
                  ->references('nokamar')->on('kamars')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penghuni_kamar');
        Schema::dropIfExists('penghunis');
    }
};