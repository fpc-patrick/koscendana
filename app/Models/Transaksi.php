<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaksi extends Model
{
    use HasFactory;

    protected $primaryKey = 'idtransaksi';
    protected $fillable = ['penghuni_id','tglbayar','statusbayar','buktibayar'];

    // Relasi ke penghuni
    public function penghuni() {
        return $this->belongsTo(Penghuni::class, 'penghuni_id', 'idpenghuni');
    }

    // Relasi ke tagihan
    public function tagihans() {
        return $this->hasMany(Tagihan::class, 'transaksi_id', 'idtransaksi');
    }

    // Ambil tagihan lama = mulai sebelum sekarang
    public function tagihanLama()
    {
        $this->load('tagihans');
        return $this->tagihans->filter(function($tag) {
            return Carbon::parse($tag->tglmulai) <= now();
        });
    }

    // Ambil tagihan baru = mulai setelah sekarang
    public function tagihanBaru()
    {
        $this->load('tagihans');
        return $this->tagihans->filter(function($tag) {
            return Carbon::parse($tag->tglmulai) > now();
        });
    }
    
}