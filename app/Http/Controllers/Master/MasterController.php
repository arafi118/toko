<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

class MasterController extends Controller
{
    public function index()
    {
        return view('master.index');
    }
}
