<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kamars', function (Blueprint $table) {
            $table->string('nokamar')->primary();
            $table->string('lantai');
            $table->tinyInteger('kapasitas');
            $table->text('fasilitas');
            $table->bigInteger('hargastandar');
            $table->enum('statuskamar',['Kosong','Terisi'])->default('Kosong');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('kamars');
    }
};