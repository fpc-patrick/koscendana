<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('barangs', function (Blueprint $table) {
            $table->string('kodebarang')->primary();
            $table->string('nokamar'); // FK ke kamars
            $table->text('keterangan')->nullable();
            $table->date('tanggallaporan')->nullable();
            $table->bigInteger('harga')->default(0);
            $table->string('status')->default('Baik'); // rusak, hilang, dll
            $table->timestamps();

            $table->foreign('nokamar')
                  ->references('nokamar')->on('kamars')
                  ->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('barangs');
    }
};