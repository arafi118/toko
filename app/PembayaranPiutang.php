<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PembayaranPiutang extends Model
{
    public $dates = ['created_at','updated_at','tgl_bayar'];
}
