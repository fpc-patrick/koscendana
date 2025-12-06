<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    protected $primaryKey = 'id_saldo';
    protected $table = 'saldos'; // Specify the table name if different from the default
    protected $fillable = ['date', 'namapengirim', 'jumlah', 'note']; // Define fillable attributes
    protected $guarded = ['id_saldo'];

    // Add relationships, custom methods, or other configurations as needed
}

