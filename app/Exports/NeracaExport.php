<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Business;
use App\Currency;
use App\BusinessLocation;
use App\RekeningRiil;

class NeracaExport implements FromView
{
    // public $labrug;

    // public function __construct($labrug)
    // {
    //     $this->labrug = $labrug;
    // }

    // public function view(): View
    // {
    // 	// $business_id = auth()->user()->business_id;

    // 	// $ra = RekeningRiil::where('posisi',1)->get();
    //     // $rp = RekeningRiil::where('posisi','!=',1)->get();
    // 	// $bl = BusinessLocation::where('business_id','=',$business_id)->first();
    //     $business_id = auth()->user()->business_id;

    // 	$ra = RekeningRiil::where('posisi',1)->get();
    //     $rp = RekeningRiil::whereNotIn('posisi',[1,4,5])->get();
    // 	$bl = BusinessLocation::where('business_id','=',$business_id)->first();
    //     $awal_bus = Business::where('id','=',$business_id)->first();
    //     $nr = new RekeningRiil;
    //     // $lr = $nr->getLabaRugi($req->tgl,$req->bln,$req->thn,$awal_bus->start_date);
    //     $lr = $this->labrug;
    //     // dd($lr);
    //     // return view('laporan.neraca.excel',['bl'=>$bl,'rekening_aktiva'=>$ra,'rekening_pasiva'=>$rp]);
    //     // ini_set('max_execution_time', 7200);
    // 	   return view('laporan.neraca.excel',['bl'=>$bl,'rekening_aktiva'=>$ra,'rekening_pasiva'=>$rp,'laba_rugi'=>$lr]);
    // }
    public function view(): View
    {
        return view('laporan.neraca.excel', [
            'invoices' => Currency::all()
        ]);
    }
}