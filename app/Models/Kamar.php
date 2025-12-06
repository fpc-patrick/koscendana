<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    use HasFactory;

    protected $primaryKey = 'nokamar';
    public $incrementing = false; // karena primary key berupa string (no kamar)
    protected $keyType = 'string';

    protected $fillable = [
        'nokamar', 'lantai', 'kapasitas', 'fasilitas', 'hargastandar', 'statuskamar'
    ];

    // Relasi ke tagihan
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class, 'nokamar', 'nokamar');
    }

    // Relasi ke barang
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'nokamar', 'nokamar');
    }

    // Relasi ke penghuni via pivot
    public function penghunis()
    {
        return $this->belongsToMany(Penghuni::class, 'penghuni_kamar', 'nokamar', 'penghuni_id')
            ->withPivot('tgl_masuk','tgl_keluar','penyewa_nama','penyewa_nohp')
            ->withTimestamps();
    }

}