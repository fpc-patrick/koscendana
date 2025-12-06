<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihans';
    protected $primaryKey = 'idtagihan';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'transaksi_id',
        'nokamar',
        'tglmulai',
        'tglberakhir',
        'tambahan',
        'harga',
        'denda',
        'statuspembayaran'
    ];

    protected $casts = [
        'tambahan' => 'array',
    ];

    // Biar aman dari JSON double decode atau nested array
    public function getTambahanAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return $decoded ?: [];
        }

        if (is_array($value)) {
            // Jika ada array di dalam array, ratakan
            $flattened = [];
            foreach ($value as $k => $v) {
                if (is_array($v)) {
                    $flattened[$k] = array_sum($v);
                } else {
                    $flattened[$k] = $v;
                }
            }
            return $flattened;
        }

        return [];
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id', 'idtransaksi');
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'nokamar', 'nokamar');
    }
}