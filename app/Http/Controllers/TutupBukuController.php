<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Rekening;
use App\RekeningRiil;
use App\Jurnal;
use App\TransactionPayment;
use App\PembayaranHutang;
use App\PembayaranPiutang;

class TutupBukuController extends Controller
{
    public function index()
    {
        $rekening_aktiva = RekeningRiil::where('posisi',1)->get();
        $rekening_passiva = RekeningRiil::whereNotIn('posisi',[1,4,5])->get();
      
    	return view('tutup_buku.index',compact('rekening_aktiva','rekening_passiva'));
    }

    public function doTutupBuku(Request $req)
    {
    	$tahun = $req->tahunan;
    	try {
    		if (!Schema::hasColumn('rekening', 'tb'.$tahun)) {

			    Schema::table('rekening', function ($table) use ($tahun) {

			        $table->double('tb'.$tahun,11)->default(0);

			    });

			    $rekenings = Rekening::all();
		    	$rekap = array();
		    	foreach ($rekenings as $key) {
		    	   $rekap_buku = $this->rekapBuku($key->jenis_mutasi,$key->kd_rekening,$tahun);
				   Rekening::where('kd_rekening',$key->kd_rekening)->update([$tahun=>$rekap_buku]);
				}
                $msg = 'Tutup buku tahun '.$tahun.' telah selesai!';		
            	}
			else{
                $rekenings = Rekening::all();
                $rekap = array();
                $bus = auth()->user()->business_id;
                // dd($bus);
		    	foreach ($rekenings as $key) {
		    	   $rekap_buku = $this->rekapBuku($key->jenis_mutasi,$key->kd_rekening,$tahun);
                   Rekening::where('kd_rekening',$key->kd_rekening)
                   ->where('business_id',auth()->user()->business_id)
                   ->update([$tahun=>$rekap_buku]);
                   dd($rekap_buku);
                }
                $msg = 'Tutup buku tahun '.$tahun.' telah selesai!';		
				// $msg = 'Tahun '.$tahun.' sudah dilakukan tutup buku';
			}
    	} catch (\Exception $e) {
    		 \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

    		 $msg = 'Terjadi kesalahan : '.$e->getMessage(); 
    	}

    	return redirect()->to('tutup_buku')->with('success',$msg);
    }

    public function rekapBuku($dk,$kd_rekening,$tahun){

    	$jurnal = Jurnal::selectRaw('COALESCE(SUM(nominal),0) as ttl')
                     ->where('business_id',auth()->user()->business_id)
                     ->where('kd_rekening_'.$dk,$kd_rekening)
                     ->whereYear('tanggal_jurnal',$tahun)
                     ->first();

        $transaksi = TransactionPayment::selectRaw('COALESCE(SUM(amount),0) as ttl')
                     ->where('business_id',auth()->user()->business_id)
                     ->where('id_rekening_'.$dk,$kd_rekening)
                     ->whereYear('paid_on',$tahun)
                     ->first();

        $pembayaran_hutang = PembayaranHutang::selectRaw('COALESCE(SUM(nominal),0) as ttl')
                     ->where('business_id',auth()->user()->business_id)
                     ->where('kd_rekening_'.$dk,$kd_rekening)
                     ->whereYear('tgl_bayar',$tahun)
                     ->first();

        $pembayaran_piutang = PembayaranPiutang::selectRaw('COALESCE(SUM(nominal),0) as ttl')
                     ->where('business_id',auth()->user()->business_id)
                     ->where('kd_rekening_'.$dk,$kd_rekening)
                     ->whereYear('tgl_bayar',$tahun)
                     ->first();

        return $jurnal->ttl + $transaksi->ttl + $pembayaran_hutang->ttl + $pembayaran_piutang->ttl;

    }
}
