<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id('idtransaksi');
            $table->unsignedBigInteger('penghuni_id'); // FK ke penghuni
            $table->date('tglbayar')->nullable();
            $table->enum('statusbayar',['Lunas','Belum Lunas'])->default('Belum Lunas');
            $table->string('buktibayar')->nullable(); // path file bukti
            $table->timestamps();

            $table->foreign('penghuni_id')
                  ->references('idpenghuni')->on('penghunis')
                  ->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('transaksis');
    }
};