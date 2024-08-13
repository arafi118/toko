<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\JenisBuku;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\StockAdjustmentLine;
use App\PurchaseLine;
use App\Jurnal;
use App\Rekening;
use Carbon\Carbon;

class RekeningRiil extends Model
{
    protected $table = 'rekening_riil';
    /*protected $primaryKey = 'kd_rekening';*/
    public $timestamps = false;

    public function getRekening($idrekening)
    {
        return JenisBuku::where('business_id', auth()->user()->business_id)->whereRaw('LEFT(kd_jb,2) = "' . $idrekening . '"')->get();
    }

    public function getPendapatanBiayaJurnal($tgl = null, $bln = null, $thn = null)
    {
        $m_now = $bln != null ? $bln : date('m');
        $y_now = $thn != null ? $thn : date('Y');

        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();

        $tgl_kondisi = date("$thn-$bln-01");
        $tgl_input = date("$thn-$bln-$tgl");
        $akhir_hari = $tgl != null ? Carbon::createFromFormat('Y-m-d', $tgl_input)->endOfDay() : "";
        $akhir_bulan_ini = $bln != null ? Carbon::createFromFormat('Y-m-d', $tgl_kondisi)->endOfMonth() : "";

        $get_pendapatan_kredit_jrn = Jurnal::selectRaw('COALESCE(SUM(nominal),0) as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(kd_rekening_kredit,2) = 41');
        if ($tgl != null) {
            // $get_pendapatan_kredit_jrn->whereDate('tanggal_jurnal',$thn.'-'.$bln.'-'.$tgl);
            $get_pendapatan_kredit_jrn->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_hari]);
        } else {
            $get_pendapatan_kredit_jrn->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_ini]);
        }

        // if($bln !=null){
        //     $get_pendapatan_kredit_jrn->whereBetween('tanggal_jurnal',[$awal_tahun,$akhir_bulan_ini]);
        // }

        // if($thn !=null){
        //     $get_pendapatan_kredit_jrn->whereYear('tanggal_jurnal',$thn);
        // }

        $pendapatan_kredit_jrn = $get_pendapatan_kredit_jrn->first();


        $get_pendapatan_debit_jrn = Jurnal::selectRaw('COALESCE(SUM(nominal),0) as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(kd_rekening_debit,2) = 41');
        if ($tgl != null) {
            // $get_pendapatan_debit_jrn->whereDate('tanggal_jurnal',$thn.'-'.$bln.'-'.$tgl);
            $get_pendapatan_debit_jrn->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_hari]);
        } else {

            $get_pendapatan_debit_jrn->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_ini]);
        }

        // if($bln !=null){
        //     $get_pendapatan_debit_jrn->whereBetween('tanggal_jurnal',[$awal_tahun,$akhir_bulan_ini]);
        // }

        // if($thn !=null){
        //     $get_pendapatan_debit_jrn->whereYear('tanggal_jurnal',$thn);
        // }

        $pendapatan_debit_jrn = $get_pendapatan_debit_jrn->first();

        $get_biaya_kredit_jrn = Jurnal::selectRaw('COALESCE(SUM(nominal),0) as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(kd_rekening_kredit,2) = 51');
        if ($tgl != null) {
            // $get_biaya_kredit_jrn->whereDate('tanggal_jurnal',$thn.'-'.$bln.'-'.$tgl);
            $get_biaya_kredit_jrn->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_hari]);
        } else {
            $get_biaya_kredit_jrn->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_ini]);
        }

        // if($bln !=null){
        //     $get_biaya_kredit_jrn->whereBetween('tanggal_jurnal',[$awal_tahun,$akhir_bulan_ini]);
        // }

        // if($thn !=null){
        //     $get_biaya_kredit_jrn->whereYear('tanggal_jurnal',$thn);
        // }

        $biaya_kredit_jrn = $get_biaya_kredit_jrn->first();

        $get_biaya_debit_jrn = Jurnal::selectRaw('COALESCE(SUM(nominal),0) as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(kd_rekening_debit,2) = 51');
        if ($tgl != null) {
            // $get_biaya_debit_jrn->whereDate('tanggal_jurnal',$thn.'-'.$bln.'-'.$tgl);
            $get_biaya_debit_jrn->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_hari]);
        } else {
            $get_biaya_debit_jrn->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_ini]);
        }

        // if($bln !=null){
        //     $get_biaya_debit_jrn->whereBetween('tanggal_jurnal',[$awal_tahun,$akhir_bulan_ini]);
        // }

        // if($thn !=null){
        //     $get_biaya_debit_jrn->whereYear('tanggal_jurnal',$thn);
        // }

        $biaya_debit_jrn = $get_biaya_debit_jrn->first();

        $pendapatan = $pendapatan_kredit_jrn->ttl - $pendapatan_debit_jrn->ttl;
        $biaya      = $biaya_debit_jrn->ttl - $biaya_kredit_jrn->ttl;

        return $pendapatan - $biaya;
    }

    public function getPendapatanBiayaTransaksi($tgl = null, $bln = null, $thn = null)
    {
        $m_now = $bln != null ? $bln : date('m');
        $y_now = $thn != null ? $thn : date('Y');

        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();

        $tgl_kondisi = date("$thn-$bln-01");
        $tgl_input = date("$thn-$bln-$tgl");
        $akhir_hari = $tgl != null ? Carbon::createFromFormat('Y-m-d', $tgl_input)->endOfDay() : "";
        $akhir_bulan_ini = $bln != null ? Carbon::createFromFormat('Y-m-d', $tgl_kondisi)->endOfMonth() : "";

        $get_pendapatan_kredit = TransactionPayment::selectRaw('COALESCE(SUM(amount),0) as ttl')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_kredit,2) = 41');
        if ($tgl != null) {
            // $get_pendapatan_kredit->whereDate('paid_on',$thn.'-'.$bln.'-'.$tgl);
            $get_pendapatan_kredit->whereBetween('paid_on', [$awal_tahun, $akhir_hari]);
        } else {
            $get_pendapatan_kredit->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_ini]);
        }

        // if($bln !=null){
        //     $get_pendapatan_kredit->whereBetween('paid_on',[$awal_tahun,$akhir_bulan_ini]);
        // }

        // if($thn !=null){
        //     $get_pendapatan_kredit->whereYear('paid_on',$thn);
        // }

        $pendapatan_kredit = $get_pendapatan_kredit->first();

        $get_pendapatan_debit = TransactionPayment::selectRaw('COALESCE(SUM(amount),0) as ttl')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_debit,2) = 41');
        if ($tgl != null) {
            // $get_pendapatan_debit->whereDate('paid_on',$thn.'-'.$bln.'-'.$tgl);
            $get_pendapatan_debit->whereBetween('paid_on', [$awal_tahun, $akhir_hari]);
        } else {
            $get_pendapatan_debit->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_ini]);
        }

        // if($bln !=null){
        //     $get_pendapatan_debit->whereBetween('paid_on',[$awal_tahun,$akhir_bulan_ini]);
        // }

        // if($thn !=null){
        //     $get_pendapatan_debit->whereYear('paid_on',$thn);
        // }

        $pendapatan_debit = $get_pendapatan_debit->first();

        $get_biaya_kredit = TransactionPayment::selectRaw('COALESCE(SUM(amount),0) as ttl')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_kredit,2) = 51');
        if ($tgl != null) {
            // $get_biaya_kredit->whereDate('paid_on',$thn.'-'.$bln.'-'.$tgl);
            $get_biaya_kredit->whereBetween('paid_on', [$awal_tahun, $akhir_hari]);
        } else {
            $get_biaya_kredit->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_ini]);
        }

        // if($bln !=null){
        //     $get_biaya_kredit->whereBetween('paid_on',[$awal_tahun,$akhir_bulan_ini]);
        // }

        // if($thn !=null){
        //     $get_biaya_kredit->whereYear('paid_on',$thn);
        // }

        $biaya_kredit = $get_biaya_kredit->first();

        $get_biaya_debit = TransactionPayment::selectRaw('COALESCE(SUM(amount),0) as ttl')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_debit,2) = 51');
        if ($tgl != null) {
            // $get_biaya_debit->whereDate('paid_on',$thn.'-'.$bln.'-'.$tgl);
            $get_biaya_debit->whereBetween('paid_on', [$awal_tahun, $akhir_hari]);
        } else {
            $get_biaya_debit->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_ini]);
        }

        // if($bln !=null){
        //     $get_biaya_debit->whereBetween('paid_on',[$awal_tahun,$akhir_bulan_ini]);
        // }

        // if($thn !=null){
        //     $get_biaya_debit->whereYear('paid_on',$thn);
        // }

        $biaya_debit = $get_biaya_debit->first();

        $pendapatan = $pendapatan_kredit->ttl - $pendapatan_debit->ttl;
        $biaya      = $biaya_debit->ttl - $biaya_kredit->ttl;

        if ($biaya < 0) {
            return $pendapatan + $biaya;
        } else {
            return $pendapatan - $biaya;
        }
    }

    public function getPendapatanBiayaStockAdjustment($tgl = null, $bln = null, $thn = null)
    {
        $m_now = $bln != null ? $bln : date('m');
        $y_now = $thn != null ? $thn : date('Y');

        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();

        $tgl_kondisi = date("$thn-$bln-01");
        $tgl_input = date("$thn-$bln-$tgl");
        $akhir_hari = $tgl != null ? Carbon::createFromFormat('Y-m-d', $tgl_input)->endOfDay() : "";
        $akhir_bulan_ini = $bln != null ? Carbon::createFromFormat('Y-m-d', $tgl_kondisi)->endOfMonth() : "";

        $get_pendapatan_kredit = StockAdjustmentLine::selectRaw('COALESCE(SUM(quantity * unit_price),0) as ttl')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_kredit,2) = 41');
        if ($tgl != null) {
            // $get_pendapatan_kredit->whereDate('stock_adjustment_lines.created_at',$thn.'-'.$bln.'-'.$tgl);
            $get_pendapatan_kredit->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_hari]);
        } else {
            $get_pendapatan_kredit->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_bulan_ini]);
        }

        // if($bln !=null){
        //     $get_pendapatan_kredit->whereBetween('stock_adjustment_lines.created_at',[$awal_tahun,$akhir_bulan_ini]);
        // }

        // if($thn !=null){
        //     $get_pendapatan_kredit->whereYear('stock_adjustment_lines.created_at',$thn);
        // }

        $pendapatan_kredit = $get_pendapatan_kredit->first();

        $get_pendapatan_debit = StockAdjustmentLine::selectRaw('COALESCE(SUM(quantity * unit_price),0) as ttl')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_debit,2) = 41');
        if ($tgl != null) {
            // $get_pendapatan_debit->whereDate('stock_adjustment_lines.created_at',$thn.'-'.$bln.'-'.$tgl);
            $get_pendapatan_debit->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_hari]);
        } else {
            $get_pendapatan_debit->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_bulan_ini]);
        }

        // if($bln !=null){
        //     $get_pendapatan_debit->whereBetween('stock_adjustment_lines.created_at',[$awal_tahun,$akhir_bulan_ini]);
        // }

        // if($thn !=null){
        //     $get_pendapatan_debit->whereYear('stock_adjustment_lines.created_at',$thn);
        // }

        $pendapatan_debit = $get_pendapatan_debit->first();

        $get_biaya_kredit = StockAdjustmentLine::selectRaw('COALESCE(SUM(quantity * unit_price),0) as ttl')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_kredit,2) = 51');
        if ($tgl != null) {
            // $get_biaya_kredit->whereDate('stock_adjustment_lines.created_at',$thn.'-'.$bln.'-'.$tgl);
            $get_biaya_kredit->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_hari]);
        } else {
            $get_biaya_kredit->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_bulan_ini]);
        }

        // if($bln !=null){
        //     $get_biaya_kredit->whereBetween('stock_adjustment_lines.created_at',[$awal_tahun,$akhir_bulan_ini]);
        // }

        // if($thn !=null){
        //     $get_biaya_kredit->whereYear('stock_adjustment_lines.created_at',$thn);
        // }

        $biaya_kredit = $get_biaya_kredit->first();

        $get_biaya_debit = StockAdjustmentLine::selectRaw('COALESCE(SUM(quantity * unit_price),0) as ttl')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_debit,2) = 51');
        if ($tgl != null) {
            // $get_biaya_debit->whereDate('stock_adjustment_lines.created_at',$thn.'-'.$bln.'-'.$tgl);
            $get_biaya_debit->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_hari]);
        } else {
            $get_biaya_debit->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_bulan_ini]);
        }

        // if($bln !=null){
        //     $get_biaya_debit->whereBetween('stock_adjustment_lines.created_at',[$awal_tahun,$akhir_bulan_ini]);
        // }

        // if($thn !=null){
        //     $get_biaya_debit->whereYear('stock_adjustment_lines.created_at',$thn);
        // }

        $biaya_debit = $get_biaya_debit->first();

        $pendapatan = $pendapatan_kredit->ttl - $pendapatan_debit->ttl;
        $biaya      = $biaya_debit->ttl - $biaya_kredit->ttl;

        if ($biaya < 0) {
            return $pendapatan + $biaya;
        } else {
            return $pendapatan - $biaya;
        }
    }

    public function getPendapatanBiayaAwal()
    {

        $get_pendapatan_kredit = Rekening::selectRaw('COALESCE(SUM(CAST(awal as SIGNED)),0)as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->where('jenis_mutasi', 'kredit')
            ->whereRaw('LEFT(kd_rekening,2) = 41');

        $pendapatan_kredit = $get_pendapatan_kredit->first();

        $get_pendapatan_debit = Rekening::selectRaw('COALESCE(SUM(CAST(awal as SIGNED)),0)as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->where('jenis_mutasi', 'debit')
            ->whereRaw('LEFT(kd_rekening,2) = 41');

        $pendapatan_debit = $get_pendapatan_debit->first();

        $get_biaya_kredit = Rekening::selectRaw('COALESCE(SUM(CAST(awal as SIGNED)),0)as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->where('jenis_mutasi', 'kredit')
            ->whereRaw('LEFT(kd_rekening,2) = 51');

        $biaya_kredit = $get_biaya_kredit->first();

        $get_biaya_debit = Rekening::selectRaw('COALESCE(SUM(CAST(awal as SIGNED)),0)as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->where('jenis_mutasi', 'debit')
            ->whereRaw('LEFT(kd_rekening,2) = 51');

        $biaya_debit = $get_biaya_debit->first();

        $pendapatan = $pendapatan_kredit->ttl - $pendapatan_debit->ttl;
        $biaya      = $biaya_debit->ttl - $biaya_kredit->ttl;

        if ($biaya < 0) {
            return $pendapatan + $biaya;
        } else {
            return $pendapatan - $biaya;
        }

        // return $pendapatan;
        // return $biaya;

    }

    public function getLabaRugi($tgl = null, $bln = null, $thn = null, $pakai)
    {
        $thn_pakai = substr($pakai, 0, 4);
        if ($thn_pakai == $thn) {
            $awal  = $this->getPendapatanBiayaAwal($tgl, $bln, $thn);
        } else {
            $awal = 0;
        }
        $jurnal    = $this->getPendapatanBiayaJurnal($tgl, $bln, $thn);
        $transaksi = $this->getPendapatanBiayaTransaksi($tgl, $bln, $thn);
        $stock_adjustment = $this->getPendapatanBiayaStockAdjustment($tgl, $bln, $thn);

        return $awal + $jurnal + $transaksi + $stock_adjustment;
    }

    public function fromTransaction($kode, $idjenisbuku, $tgl = null, $bln = null, $thn = null, $period = null, $dk = false)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        // $awal_tahun       = $thn != null ? Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear() : Carbon::now()->startOfYear();
        // $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('m', $bln)->subMonth(1)->endOfMonth() : Carbon::now()->subMonth(1)->endOfMonth();
        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        $tgl_kondisi = date("$thn-$bln-01");
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";

        $get_debit = Transaction::selectRaw('SUM(final_total - shipping_charges) as ttl')
            ->where('business_id', auth()->user()->business_id)
            //    ->where('payment_status',['paid'])
            ->whereRaw('LEFT(kd_rekening_debit,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            $get_debit->whereDate('transaction_date', $thn . '-' . $bln . '-' . $tgl);
        }

        if ($bln != null) {
            $get_debit->whereMonth('transaction_date', $bln);
        }

        if ($thn != null) {
            $get_debit->whereYear('transaction_date', $thn);
        }

        $debit    = $get_debit->first();

        $get_kredit   = Transaction::selectRaw('SUM(final_total - shipping_charges) as ttl')
            ->where('business_id', auth()->user()->business_id)
            //    ->where('payment_status',['paid'])
            ->whereRaw('LEFT(kd_rekening_kredit,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            $get_kredit->whereDate('transaction_date', $thn . '-' . $bln . '-' . $tgl);
        }

        if ($bln != null) {
            $get_kredit->whereMonth('transaction_date', $bln);
        }

        if ($thn != null) {
            $get_kredit->whereYear('transaction_date', $thn);
        }

        $kredit = $get_kredit->first();

        $total = 0;
        if ($kode == 'aktiva') {
            $total = $debit->ttl - $kredit->ttl;
        } else {
            $total = $kredit->ttl - $debit->ttl;
        }

        if ($dk) {
            return [
                "kredit" => $kredit->ttl,
                "debit" => $debit->ttl,
                "total" => $total
            ];
        }
        return $total;
    }


    public function fromTransactionAwalBulan($kode, $idjenisbuku, $tgl = null, $bln = null, $thn = null, $period = null, $dk = false)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        // $awal_tahun       = $thn != null ? Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear() : Carbon::now()->startOfYear();
        // $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('m', $bln)->subMonth(1)->endOfMonth() : Carbon::now()->subMonth(1)->endOfMonth();
        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        $tgl_kondisi = date("$thn-$bln-01");
        $tgl_input = date("$thn-$bln-$tgl");
        $akhir_hari = $tgl != null ? Carbon::createFromFormat('Y-m-d', $tgl_input)->startOfDay() : "";
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";

        $get_debit = Transaction::selectRaw('SUM(final_total - shipping_charges) as ttl')
            ->where('business_id', auth()->user()->business_id)
            //    ->where('payment_status',['paid'])
            ->whereRaw('LEFT(kd_rekening_debit,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            // $get_debit->whereDate('transaction_date',$thn.'-'.$bln.'-'.$tgl);
            $get_debit->where('transaction_date', '>=', $awal_tahun)
                ->where('transaction_date', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_lalu') {
            $get_debit->whereBetween('transaction_date', [$awal_tahun, $akhir_bulan_lalu]);
        }

        // if($bln !=null){
        //     if($period == 'sd_bulan_lalu'){
        //         $get_debit->whereBetween('transaction_date',[$awal_tahun,$akhir_bulan_lalu]);
        //         $get_debit->whereYear('transaction_date',$thn);

        //     }else{
        //         $get_debit->whereMonth('transaction_date',$bln);
        //     }
        // }

        if ($thn != null) {
            $get_debit->whereYear('transaction_date', $thn);
        }

        $debit    = $get_debit->first();

        $get_kredit   = Transaction::selectRaw('SUM(final_total - shipping_charges) as ttl')
            ->where('business_id', auth()->user()->business_id)
            //    ->where('payment_status',['paid'])
            ->whereRaw('LEFT(kd_rekening_kredit,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            // $get_kredit->whereDate('transaction_date',$thn.'-'.$bln.'-'.$tgl);
            $get_kredit->where('transaction_date', '>=', $awal_tahun)
                ->where('transaction_date', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_lalu') {

            $get_kredit->whereBetween('transaction_date', [$awal_tahun, $akhir_bulan_lalu]);
        }

        // if($bln !=null){
        //     if($period == 'sd_bulan_lalu'){
        //         $get_kredit->whereBetween('transaction_date',[$awal_tahun,$akhir_bulan_lalu]);
        //         $get_kredit->whereYear('transaction_date',$thn);
        //     }else{
        //         $get_kredit->whereMonth('transaction_date',$bln);
        //     }
        // }

        if ($thn != null) {
            $get_kredit->whereYear('transaction_date', $thn);
        }

        $kredit = $get_kredit->first();

        $total = 0;
        if ($kode == 'aktiva') {
            $total = $debit->ttl - $kredit->ttl;
        } else {
            $total = $kredit->ttl - $debit->ttl;
        }

        if ($dk) {
            return [
                "kredit" => $kredit->ttl,
                "debit" => $debit->ttl,
                "total" => $total
            ];
        }
        return $total;
    }


    public function fromShippingCharges($kode, $idjenisbuku, $tgl = null, $bln = null, $thn = null, $period = null, $dk = false)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        // $awal_tahun       = $thn != null ? Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear() : Carbon::now()->startOfYear();
        // $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('m', $bln)->subMonth(1)->endOfMonth() : Carbon::now()->subMonth(1)->endOfMonth();
        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        $tgl_kondisi = date("$thn-$bln-01");
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";

        $get_debit = Transaction::selectRaw('SUM(shipping_charges) as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(kd_rekening_debit_htg_biaya_kirim,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            $get_debit->whereDate('transaction_date', $thn . '-' . $bln . '-' . $tgl);
        }

        if ($bln != null) {
            $get_debit->whereMonth('transaction_date', $bln);
        }

        if ($thn != null) {
            $get_debit->whereYear('transaction_date', $thn);
        }

        $debit    = $get_debit->first();

        $get_kredit   = Transaction::selectRaw('SUM(shipping_charges) as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(kd_rekening_kredit_htg_biaya_kirim,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            $get_kredit->whereDate('transaction_date', $thn . '-' . $bln . '-' . $tgl);
        }

        if ($bln != null) {
            if ($period == 'sd_bulan_lalu') {
                $get_kredit->whereBetween('transaction_date', [$awal_tahun, $akhir_bulan_lalu]);
            } else {
                $get_kredit->whereMonth('transaction_date', $bln);
            }
        }

        if ($thn != null) {
            $get_kredit->whereYear('transaction_date', $thn);
        }

        $kredit = $get_kredit->first();

        $total = 0;
        if ($kode == 'aktiva') {
            $total = $debit->ttl - $kredit->ttl;
        } else {
            $total = $kredit->ttl - $debit->ttl;
        }

        if ($dk) {
            return [
                "kredit" => $kredit->ttl,
                "debit" => $debit->ttl,
                "total" => $total
            ];
        }
        return $total;
    }


    public function fromShippingChargesAwalBulan($kode, $idjenisbuku, $tgl = null, $bln = null, $thn = null, $period = null, $dk = false)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        // $awal_tahun       = $thn != null ? Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear() : Carbon::now()->startOfYear();
        // $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('m', $bln)->subMonth(1)->endOfMonth() : Carbon::now()->subMonth(1)->endOfMonth();
        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        $tgl_kondisi = date("$thn-$bln-01");
        $tgl_input = date("$thn-$bln-$tgl");
        $akhir_hari = $tgl != null ? Carbon::createFromFormat('Y-m-d', $tgl_input)->startOfDay() : "";
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";

        $get_debit = Transaction::selectRaw('SUM(shipping_charges) as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(kd_rekening_debit_htg_biaya_kirim,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            // $get_debit->whereDate('transaction_date',$thn.'-'.$bln.'-'.$tgl);
            $get_debit->where('transaction_date', '>=', $awal_tahun)
                ->where('transaction_date', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_lalu') {

            $get_debit->whereBetween('transaction_date', [$awal_tahun, $akhir_bulan_lalu]);
        }

        // if($bln !=null){
        //     if($period == 'sd_bulan_lalu'){
        //         $get_debit->whereBetween('transaction_date',[$awal_tahun,$akhir_bulan_lalu]);
        //     }else{
        //         $get_debit->whereMonth('transaction_date',$bln);
        //     }
        // }

        if ($thn != null) {
            $get_debit->whereYear('transaction_date', $thn);
        }

        $debit    = $get_debit->first();

        $get_kredit   = Transaction::selectRaw('SUM(shipping_charges) as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(kd_rekening_kredit_htg_biaya_kirim,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            // $get_kredit->whereDate('transaction_date',$thn.'-'.$bln.'-'.$tgl);
            $get_kredit->where('transaction_date', '>=', $awal_tahun)
                ->where('transaction_date', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_lalu') {

            $get_kredit->whereBetween('transaction_date', [$awal_tahun, $akhir_bulan_lalu]);
        }

        // if($bln !=null){
        //     if($period == 'sd_bulan_lalu'){
        //         $get_kredit->whereBetween('transaction_date',[$awal_tahun,$akhir_bulan_lalu]);
        //     }else{
        //         $get_kredit->whereMonth('transaction_date',$bln);
        //     }
        // }

        if ($thn != null) {
            $get_kredit->whereYear('transaction_date', $thn);
        }

        $kredit = $get_kredit->first();

        $total = 0;
        if ($kode == 'aktiva') {
            $total = $debit->ttl - $kredit->ttl;
        } else {
            $total = $kredit->ttl - $debit->ttl;
        }

        if ($dk) {
            return [
                "kredit" => $kredit->ttl,
                "debit" => $debit->ttl,
                "total" => $total
            ];
        }
        return $total;
    }


    public function fromTransactionPayment($kode, $idjenisbuku, $tgl = null, $bln = null, $thn = null, $period = null, $dk = false)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        // $awal_tahun       = $thn != null ? Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear() : Carbon::now()->startOfYear();
        // $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('m', $bln)->subMonth(1)->endOfMonth() : Carbon::now()->subMonth(1)->endOfMonth();
        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        $tgl_kondisi = date("$thn-$bln-01");
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";


        $get_debit = TransactionPayment::selectRaw('SUM(amount) as ttl')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_debit,3) = "' . $idjenisbuku . '"');
        if ($tgl != null) {
            $get_debit->whereDate('paid_on', $thn . '-' . $bln . '-' . $tgl);
        }

        if ($bln != null) {
            $get_debit->whereMonth('paid_on', $bln);
        }

        if ($thn != null) {
            $get_debit->whereYear('paid_on', $thn);
        }

        $debit    = $get_debit->first();

        $get_kredit   = TransactionPayment::selectRaw('SUM(amount) as ttl')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_kredit,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            $get_kredit->whereDate('paid_on', $thn . '-' . $bln . '-' . $tgl);
        }
        if ($bln != null) {
            $get_kredit->whereMonth('paid_on', $bln);
        }
        if ($thn != null) {
            $get_kredit->whereYear('paid_on', $thn);
        }

        $kredit = $get_kredit->first();

        $total = 0;
        if ($kode == 'aktiva') {
            $total = $debit->ttl - $kredit->ttl;
        } else {
            $total = $kredit->ttl - $debit->ttl;
        }

        if ($dk) {
            return [
                "kredit" => $kredit->ttl,
                "debit" => $debit->ttl,
                "total" => $total
            ];
        }
        return $total;
    }


    public function fromTransactionPaymentAwalBulan($kode, $idjenisbuku, $tgl = null, $bln = null, $thn = null, $period = null, $dk = false)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        // $awal_tahun       = $thn != null ? Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear() : Carbon::now()->startOfYear();
        // $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('m', $bln)->subMonth(1)->endOfMonth() : Carbon::now()->subMonth(1)->endOfMonth();
        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        $tgl_kondisi = date("$thn-$bln-01");
        $tgl_input = date("$thn-$bln-$tgl");
        $akhir_hari = $tgl != null ? Carbon::createFromFormat('Y-m-d', $tgl_input)->startOfDay() : "";
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";

        $get_debit = TransactionPayment::selectRaw('SUM(amount) as ttl')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_debit,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            // $get_debit->whereDate('paid_on',$thn.'-'.$bln.'-'.$tgl);
            $get_debit->where('paid_on', '>=', $awal_tahun)
                ->where('paid_on', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_lalu') {
            $get_debit->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_lalu]);
        }

        // if($bln !=null){
        //     if($period == 'sd_bulan_lalu'){
        //         $get_debit->whereBetween('paid_on',[$awal_tahun,$akhir_bulan_lalu]);
        //         $get_debit->whereYear('paid_on',$y_now);
        //     }else{
        //         $get_debit->whereMonth('paid_on',$bln);
        //     }
        // }

        if ($thn != null) {
            $get_debit->whereYear('paid_on', $thn);
        }

        $debit    = $get_debit->first();

        $get_kredit   = TransactionPayment::selectRaw('SUM(amount) as ttl')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_kredit,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            // $get_kredit->whereDate('paid_on',$thn.'-'.$bln.'-'.$tgl);
            $get_kredit->where('paid_on', '>=', $awal_tahun)
                ->where('paid_on', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_lalu') {
            $get_kredit->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_lalu]);
        }
        // if($bln !=null){
        //     if($period == 'sd_bulan_lalu'){
        //         $get_kredit->whereBetween('paid_on',[$awal_tahun,$akhir_bulan_lalu]);
        //         $get_kredit->whereYear('paid_on',$y_now);
        //     }else{
        //         $get_kredit->whereMonth('paid_on',$bln);
        //     }
        // }
        if ($thn != null) {
            $get_kredit->whereYear('paid_on', $thn);
        }

        $kredit = $get_kredit->first();

        $total = 0;
        if ($kode == 'aktiva') {
            $total = $debit->ttl - $kredit->ttl;
        } else {
            $total = $kredit->ttl - $debit->ttl;
        }

        if ($dk) {
            return [
                "kredit" => $kredit->ttl,
                "debit" => $debit->ttl,
                "total" => $total
            ];
        }
        return $total;
    }


    public function fromJurnal($kode, $idjenisbuku, $tgl = null, $bln = null, $thn = null, $period = null, $dk = false)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        // $awal_tahun       = $thn != null ? Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear() : Carbon::now()->startOfYear();
        // $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('m', $bln)->subMonth(1)->endOfMonth() : Carbon::now()->subMonth(1)->endOfMonth();
        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        $tgl_kondisi = date("$thn-$bln-01");
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";

        $get_debit = Jurnal::selectRaw('SUM(nominal) as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(kd_rekening_debit,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            $get_debit->whereDate('tanggal_jurnal', $thn . '-' . $bln . '-' . $tgl);
        }

        if ($bln != null) {
            $get_debit->whereMonth('tanggal_jurnal', $bln);
        }

        if ($thn != null) {
            $get_debit->whereYear('tanggal_jurnal', $thn);
        }

        $debit = $get_debit->first();

        $get_kredit = Jurnal::selectRaw('SUM(nominal) as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(kd_rekening_kredit,3) = "' . $idjenisbuku . '"');


        if ($tgl != null) {
            $get_kredit->whereDate('tanggal_jurnal', $thn . '-' . $bln . '-' . $tgl);
        }

        if ($bln != null) {
            $get_kredit->whereMonth('tanggal_jurnal', $bln);
        }

        if ($thn != null) {
            $get_kredit->whereYear('tanggal_jurnal', $thn);
        }

        $kredit = $get_kredit->first();

        $total = 0;
        if ($kode == 'aktiva') {
            $total = $debit->ttl - $kredit->ttl;
        } else {
            $total = $kredit->ttl - $debit->ttl;
        }

        if ($dk) {
            return [
                "kredit" => $kredit->ttl,
                "debit" => $debit->ttl,
                "total" => $total
            ];
        }
        return $total;
    }


    public function fromJurnalAwalBulan($kode, $idjenisbuku, $tgl = null, $bln = null, $thn = null, $period = null, $dk = false)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        // $awal_tahun       = $thn != null ? Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear() : Carbon::now()->startOfYear();
        // $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('m', $bln)->subMonth(1)->endOfMonth() : Carbon::now()->subMonth(1)->endOfMonth();
        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        $tgl_kondisi = date("$thn-$bln-01");
        $tgl_input = date("$thn-$bln-$tgl");
        $akhir_hari = $tgl != null ? Carbon::createFromFormat('Y-m-d', $tgl_input)->startOfDay() : "";
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";

        $get_debit = Jurnal::selectRaw('SUM(nominal) as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(kd_rekening_debit,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            // $get_debit->whereDate('tanggal_jurnal',$thn.'-'.$bln.'-'.$tgl);
            $get_debit->where('tanggal_jurnal', '>=', $awal_tahun)
                ->where('tanggal_jurnal', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_lalu') {

            $get_debit->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_lalu]);
        }

        // if($bln !=null){
        //     if($period == 'sd_bulan_lalu'){
        //         $get_debit->whereBetween('tanggal_jurnal',[$awal_tahun,$akhir_bulan_lalu]);
        //         $get_debit->whereYear('tanggal_jurnal',$y_now);
        //     }else{
        //         $get_debit->whereMonth('tanggal_jurnal',$bln);
        //     }
        // }

        if ($thn != null) {
            $get_debit->whereYear('tanggal_jurnal', $thn);
        }

        $debit = $get_debit->first();

        $get_kredit = Jurnal::selectRaw('SUM(nominal) as ttl')
            ->where('business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(kd_rekening_kredit,3) = "' . $idjenisbuku . '"');


        if ($tgl != null) {
            // $get_kredit->whereDate('tanggal_jurnal',$thn.'-'.$bln.'-'.$tgl);
            $get_kredit->where('tanggal_jurnal', '>=', $awal_tahun)
                ->where('tanggal_jurnal', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_lalu') {

            $get_kredit->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_lalu]);
        }

        // if($bln !=null){
        //    if($period == 'sd_bulan_lalu'){
        //        $get_kredit->whereBetween('tanggal_jurnal',[$awal_tahun,$akhir_bulan_lalu]);
        //        $get_kredit->whereYear('tanggal_jurnal',$y_now);
        //    }else{
        //         $get_kredit->whereMonth('tanggal_jurnal',$bln);
        //    }
        // }

        if ($thn != null) {
            $get_kredit->whereYear('tanggal_jurnal', $thn);
        }

        $kredit = $get_kredit->first();

        $total = 0;
        if ($kode == 'aktiva') {
            $total = $debit->ttl - $kredit->ttl;
        } else {
            $total = $kredit->ttl - $debit->ttl;
        }

        if ($dk) {
            return [
                "kredit" => $kredit->ttl,
                "debit" => $debit->ttl,
                "total" => $total
            ];
        }
        return $total;
    }


    public function fromReturPenjualan($kode, $idjenisbuku, $tgl = null, $bln = null, $thn = null, $period = null)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        // $awal_tahun       = $thn != null ? Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear() : Carbon::now()->startOfYear();
        // $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('m', $bln)->subMonth(1)->endOfMonth() : Carbon::now()->subMonth(1)->endOfMonth();
        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m', $thn . "-" . $bln)->subMonth(1)->endOfMonth() : "";

        $get_debit = TransactionSellLine::selectRaw('SUM(quantity_returned * unit_price_inc_tax) as ttl')
            ->join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_debit,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            $get_debit->whereDate('transaction_sell_lines.created_at', $thn . '-' . $bln . '-' . $tgl);
        }

        if ($bln != null) {
            if ($period == 'sd_bulan_lalu') {
                $get_debit->whereBetween('transaction_sell_lines.created_at', [$awal_tahun, $akhir_bulan_lalu]);
            } else {
                $get_debit->whereMonth('transaction_sell_lines.created_at', $bln);
            }
        }

        if ($thn != null) {
            $get_debit->whereYear('transaction_sell_lines.created_at', $thn);
        }

        $debit = $get_debit->first();

        $get_kredit = TransactionSellLine::selectRaw('SUM(quantity_returned * unit_price_inc_tax) as ttl')
            ->join('transactions', 'transaction_sell_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_kredit,3) = "' . $idjenisbuku . '"');


        if ($tgl != null) {
            $get_kredit->whereDate('transaction_sell_lines.created_at', $thn . '-' . $bln . '-' . $tgl);
        }

        if ($bln != null) {
            if ($period == 'sd_bulan_lalu') {
                $get_kredit->whereBetween('transaction_sell_lines.created_at', [$awal_tahun, $akhir_bulan_lalu]);
            } else {
                $get_kredit->whereMonth('transaction_sell_lines.created_at', $bln);
            }
        }

        if ($thn != null) {
            $get_kredit->whereYear('transaction_sell_lines.created_at', $thn);
        }

        $kredit = $get_kredit->first();

        $total = 0;
        if ($kode == 'aktiva') {
            $total = $debit->ttl - $kredit->ttl;
        } else {
            $total = $kredit->ttl - $debit->ttl;
        }

        return $total;
    }

    public function fromReturPembelian($kode, $idjenisbuku, $tgl = null, $bln = null, $thn = null, $period = null)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        // $awal_tahun       = $thn != null ? Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear() : Carbon::now()->startOfYear();
        // $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('m', $bln)->subMonth(1)->endOfMonth() : Carbon::now()->subMonth(1)->endOfMonth();
        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m', $thn . "-" . $bln)->subMonth(1)->endOfMonth() : "";

        $get_debit = PurchaseLine::selectRaw('SUM(quantity_returned * purchase_price_inc_tax) as ttl')
            ->join('transactions', 'purchase_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_debit,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            $get_debit->whereDate('purchase_lines.created_at', $thn . '-' . $bln . '-' . $tgl);
        }

        if ($bln != null) {
            if ($period == 'sd_bulan_lalu') {
                $get_debit->whereBetween('purchase_lines.created_at', [$awal_tahun, $akhir_bulan_lalu]);
            } else {
                $get_debit->whereMonth('purchase_lines.created_at', $bln);
            }
        }

        if ($thn != null) {
            $get_debit->whereYear('purchase_lines.created_at', $thn);
        }

        $debit = $get_debit->first();

        $get_kredit = PurchaseLine::selectRaw('SUM(quantity_returned * purchase_price_inc_tax) as ttl')
            ->join('transactions', 'purchase_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_kredit,3) = "' . $idjenisbuku . '"');


        if ($tgl != null) {
            $get_kredit->whereDate('purchase_lines.created_at', $thn . '-' . $bln . '-' . $tgl);
        }

        if ($bln != null) {
            if ($period == 'sd_bulan_lalu') {
                $get_kredit->whereBetween('purchase_lines.created_at', [$awal_tahun, $akhir_bulan_lalu]);
            } else {
                $get_kredit->whereMonth('purchase_lines.created_at', $bln);
            }
        }

        if ($thn != null) {
            $get_kredit->whereYear('purchase_lines.created_at', $thn);
        }

        $kredit = $get_kredit->first();

        $total = 0;
        if ($kode == 'aktiva') {
            $total = $debit->ttl - $kredit->ttl;
        } else {
            $total = $kredit->ttl - $debit->ttl;
        }

        return $total;
    }

    public function fromStockAdjustment($kode, $idjenisbuku, $tgl = null, $bln = null, $thn = null, $period = null, $dk = false)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        // $awal_tahun       = $thn != null ? Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear() : Carbon::now()->startOfYear();
        // $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('m', $bln)->subMonth(1)->endOfMonth() : Carbon::now()->subMonth(1)->endOfMonth();
        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        $tgl_kondisi = date("$thn-$bln-01");
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";

        $get_debit = StockAdjustmentLine::selectRaw('SUM(quantity * unit_price) as ttl')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_debit,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            $get_debit->whereDate('stock_adjustment_lines.created_at', $thn . '-' . $bln . '-' . $tgl);
        }

        if ($bln != null) {
            $get_debit->whereMonth('stock_adjustment_lines.created_at', $bln);
        }

        if ($thn != null) {
            $get_debit->whereYear('stock_adjustment_lines.created_at', $thn);
        }

        $debit = $get_debit->first();

        $get_kredit = StockAdjustmentLine::selectRaw('SUM(quantity * unit_price) as ttl')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_kredit,3) = "' . $idjenisbuku . '"');


        if ($tgl != null) {
            $get_kredit->whereDate('stock_adjustment_lines.created_at', $thn . '-' . $bln . '-' . $tgl);
        }

        if ($bln != null) {
            if ($period == 'sd_bulan_lalu') {
                $get_kredit->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_bulan_lalu]);
                $get_kredit->whereYear('stock_adjustment_lines.created_at', $y_now);
            } else {
                $get_kredit->whereMonth('stock_adjustment_lines.created_at', $bln);
            }
        }

        if ($thn != null) {
            $get_kredit->whereYear('stock_adjustment_lines.created_at', $thn);
        }

        $kredit = $get_kredit->first();

        $total = 0;
        if ($kode == 'aktiva') {
            $total = $debit->ttl - $kredit->ttl;
        } else {
            $total = $kredit->ttl - $debit->ttl;
        }

        if ($dk) {
            return [
                "kredit" => $kredit->ttl,
                "debit" => $debit->ttl,
                "total" => $total
            ];
        }
        return $total;
    }

    public function fromStockAdjustmentAwalBulan($kode, $idjenisbuku, $tgl = null, $bln = null, $thn = null, $period = null, $dk = false)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        // $awal_tahun       = $thn != null ? Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear() : Carbon::now()->startOfYear();
        // $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('m', $bln)->subMonth(1)->endOfMonth() : Carbon::now()->subMonth(1)->endOfMonth();
        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        $tgl_kondisi = date("$thn-$bln-01");
        $tgl_input = date("$thn-$bln-$tgl");
        $akhir_hari = $tgl != null ? Carbon::createFromFormat('Y-m-d', $tgl_input)->startOfDay() : "";
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";

        $get_debit = StockAdjustmentLine::selectRaw('SUM(quantity * unit_price) as ttl')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_debit,3) = "' . $idjenisbuku . '"');

        if ($tgl != null) {
            // $get_debit->whereDate('stock_adjustment_lines.created_at',$thn.'-'.$bln.'-'.$tgl);
            $get_debit->where('stock_adjustment_lines.created_at', '>=', $awal_tahun)
                ->where('stock_adjustment_lines.created_at', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_lalu') {

            $get_debit->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_bulan_lalu]);
        }

        // if($bln !=null){
        //     if($period == 'sd_bulan_lalu'){
        //         $get_debit->whereBetween('stock_adjustment_lines.created_at',[$awal_tahun,$akhir_bulan_lalu]);
        //         $get_debit->whereYear('stock_adjustment_lines.created_at',$y_now);
        //     }else{
        //         $get_debit->whereMonth('stock_adjustment_lines.created_at',$bln);
        //     }
        // }

        if ($thn != null) {
            $get_debit->whereYear('stock_adjustment_lines.created_at', $thn);
        }

        $debit = $get_debit->first();

        $get_kredit = StockAdjustmentLine::selectRaw('SUM(quantity * unit_price) as ttl')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->whereRaw('LEFT(id_rekening_kredit,3) = "' . $idjenisbuku . '"');


        if ($tgl != null) {
            // $get_kredit->whereDate('stock_adjustment_lines.created_at',$thn.'-'.$bln.'-'.$tgl);
            $get_kredit->where('stock_adjustment_lines.created_at', '>=', $awal_tahun)
                ->where('stock_adjustment_lines.created_at', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_lalu') {

            $get_kredit->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_bulan_lalu]);
        }

        // if($bln !=null){
        //    if($period == 'sd_bulan_lalu'){
        //         $get_kredit->whereBetween('stock_adjustment_lines.created_at',[$awal_tahun,$akhir_bulan_lalu]);
        //         $get_kredit->whereYear('stock_adjustment_lines.created_at',$y_now);
        //     }else{
        //         $get_kredit->whereMonth('stock_adjustment_lines.created_at',$bln);
        //    }
        // }

        if ($thn != null) {
            $get_kredit->whereYear('stock_adjustment_lines.created_at', $thn);
        }

        $kredit = $get_kredit->first();

        $total = 0;
        if ($kode == 'aktiva') {
            $total = $debit->ttl - $kredit->ttl;
        } else {
            $total = $kredit->ttl - $debit->ttl;
        }

        if ($dk) {
            return [
                "kredit" => $kredit->ttl,
                "debit" => $debit->ttl,
                "total" => $total
            ];
        }
        return $total;
    }

    public function getValueNeraca($kode, $idjenisbuku, $tgl = null, $bln = null, $thn = null, $period = null)
    {
        $transaction        = $this->fromTransaction($kode, $idjenisbuku, $tgl, $bln, $thn, $period);
        $transaction_payment = $this->fromTransactionPayment($kode, $idjenisbuku, $tgl, $bln, $thn, $period);
        $jurnal             = $this->fromJurnal($kode, $idjenisbuku, $tgl, $bln, $thn, $period);
        $shipping_charges   = $this->fromShippingCharges($kode, $idjenisbuku, $tgl, $bln, $thn, $period);
        // $retur_penjualan    = $this->fromReturPenjualan($kode,$idjenisbuku,$tgl,$bln,$thn,$period);
        // $retur_pembelian    = $this->fromReturPembelian($kode,$idjenisbuku,$tgl,$bln,$thn,$period);
        $stock_adjustment_lines = $this->fromStockAdjustment($kode, $idjenisbuku, $tgl, $bln, $thn, $period);

        $total = $transaction + $transaction_payment + $jurnal + $shipping_charges + $stock_adjustment_lines;
        // $total = $transaction + $transaction_payment + $jurnal + $shipping_charges + $retur_penjualan + $retur_pembelian + $stock_adjustment_lines;

        return $total;
    }


    public function getValueNeracaAwalBulan($kode, $idjenisbuku, $tgl = null, $bln = null, $thn = null, $period = null)
    {
        $transaction_awl_bln            = $this->fromTransactionAwalBulan($kode, $idjenisbuku, $tgl, $bln, $thn, $period);
        $transaction_payment_awl_bln    = $this->fromTransactionPaymentAwalBulan($kode, $idjenisbuku, $tgl, $bln, $thn, $period);
        $jurnal_awl_bln                 = $this->fromJurnalAwalBulan($kode, $idjenisbuku, $tgl, $bln, $thn, $period);
        $shipping_charges_awl_bln       = $this->fromShippingChargesAwalBulan($kode, $idjenisbuku, $tgl, $bln, $thn, $period);
        // $retur_penjualan    = $this->fromReturPenjualan($kode,$idjenisbuku,$tgl,$bln,$thn,$period);
        // $retur_pembelian    = $this->fromReturPembelian($kode,$idjenisbuku,$tgl,$bln,$thn,$period);
        $stock_adjustment_lines_awl_bln = $this->fromStockAdjustmentAwalBulan($kode, $idjenisbuku, $tgl, $bln, $thn, $period);

        $total = $transaction_awl_bln + $transaction_payment_awl_bln + $jurnal_awl_bln + $shipping_charges_awl_bln + $stock_adjustment_lines_awl_bln;
        // $total = $transaction + $transaction_payment + $jurnal + $shipping_charges + $retur_penjualan + $retur_pembelian + $stock_adjustment_lines;

        return $total;
    }
}
