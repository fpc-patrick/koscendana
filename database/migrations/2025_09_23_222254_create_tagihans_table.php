<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id('idtagihan');
            $table->unsignedBigInteger('transaksi_id'); // FK ke transaksis
            $table->string('nokamar'); // FK ke kamars
            $table->date('tglmulai')->nullable();
            $table->date('tglberakhir')->nullable();
            $table->json('tambahan')->nullable(); // ['tv'=>100000, 'kulkas'=>150000]
            $table->bigInteger('harga')->default(0);
            $table->bigInteger('denda')->default(0); // ✅ Kolom baru untuk menyimpan denda keterlambatan
            $table->enum('statuspembayaran',['Lunas','Belum Lunas'])->default('Belum Lunas');
            $table->timestamps();

            $table->foreign('transaksi_id')
                  ->references('idtransaksi')->on('transaksis')
                  ->onDelete('cascade');

            $table->foreign('nokamar')
                  ->references('nokamar')->on('kamars')
                  ->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('tagihans');
    }
};