<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $table   = 'jurnal';
    
    protected $dates   = ['tanggal_jurnal'];
}
