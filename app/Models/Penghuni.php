<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penghuni extends Model
{
    use HasFactory;

    protected $primaryKey = 'idpenghuni';
    protected $fillable = [
        'kode_penghuni','nama','nohp','nohp_orangtua','alamat','fotoktp','status_penghuni'
    ];

    // Relasi many-to-many ke Kamar
    public function kamars()
    {
        return $this->belongsToMany(Kamar::class, 'penghuni_kamar', 'penghuni_id', 'nokamar')
            ->withPivot('tgl_masuk','tgl_keluar','penyewa_nama','penyewa_nohp')
            ->withTimestamps();
    }
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'penghuni_id', 'idpenghuni');
    }
}