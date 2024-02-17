<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NeracaAwalBulan extends Model
{
    protected $table = 'neraca_awal_bulan';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
    protected $dates = [
        'tanggal',
    ];
}
