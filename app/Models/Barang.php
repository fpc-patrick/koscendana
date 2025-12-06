<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $primaryKey = 'kodebarang';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['kodebarang','nokamar','keterangan','tanggallaporan','harga','status'];

    // Relasi ke kamar
    public function kamar() {
        return $this->belongsTo(Kamar::class, 'nokamar', 'nokamar');
    }
}