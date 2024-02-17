<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SimpanBarang extends Model
{
    //
    protected $table = 'simpan_barang';
    protected $guarded = ['id'];
    // protected $primaryKey = 'id';
    public $timestamps = false;
}
