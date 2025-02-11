<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Rekening;
use App\Jurnal;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\StockAdjustmentLine;
use App\PurchaseLine;
use App\Transaction;
use App\Business;
use Carbon\Carbon;

class JenisBuku extends Model
{
    protected $table = 'jenis_buku';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function getRekeningByIdJenisBuku($idjenisbuku)
    {
        $rekening =    Rekening::where('business_id', auth()->user()->business_id)->where('kd_jb', $idjenisbuku)->get();

        return $rekening;
    }

    public function getSaldoAwal($kd_jenis_buku, $jenis_mutasi, $thn = null)
    {
        $th_lalu = $thn - 1;
        $saldo = Rekening::selectRaw('COALESCE(SUM(tb' . $th_lalu . '),0) as ttltahunlalu,COALESCE(SUM(awal),0) as ttlawalpakai')
            ->where('business_id', auth()->user()->business_id)
            ->where('kd_jb', $kd_jenis_buku)
            ->where('jenis_mutasi', $jenis_mutasi)
            ->first();

        return $saldo;
    }

    public function getValueAwalByRekening($kd_rekening)
    {
        $awal = Rekening::selectRaw('CAST(awal as SIGNED) awal')
            ->where('business_id', auth()->user()->business_id)
            ->where('kd_rekening', $kd_rekening)
            ->first();

        return $awal->awal == '' ? 0 : $awal->awal;
    }


    public function LbRgSdBlnLalu($idrekening, $pb, $tgl = null, $bln = null, $thn = null, $period = null)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        $awal_tahun       = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();

        $tgl_kondisi = date("$thn-$bln-01");
        $tgl_input = date("$thn-$bln-$tgl");
        $awal_hari = $tgl != null ? Carbon::createFromFormat('Y-m-d', $tgl_input)->startOfDay() : "";
        // $hari_minus = date('Y-m-d', strtotime('-1 days', strtotime($tgl_input)));
        // $hari_awal = $tgl !=null ? Carbon::createFromFormat('Y-m-d', $hari_minus)->endOfDay() : "";
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_ini = $bln != null ? Carbon::createFromFormat('Y-m-d', $tgl_kondisi)->endOfMonth() : "";
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";

        $jlbrg_sdblnlalu_kredit = Jurnal::selectRaw('SUM(nominal) as ttl')
            ->where('business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $jlbrg_sdblnlalu_kredit->where('tanggal_jurnal', '>=', $awal_tahun)
                ->where('tanggal_jurnal', '<', $awal_hari);
        } elseif ($period == 'sd_bulan_lalu') {
            $jlbrg_sdblnlalu_kredit->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_lalu]);
        }

        $jlbrg_sdblnlalu_kredit->where('kd_rekening_kredit', $idrekening);


        $jurnal_kredit = $jlbrg_sdblnlalu_kredit->first();

        $jlbrg_sdblnlalu_debit = Jurnal::selectRaw('SUM(nominal) as ttl')
            ->where('business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $jlbrg_sdblnlalu_debit->where('tanggal_jurnal', '>=', $awal_tahun)
                ->where('tanggal_jurnal', '<', $awal_hari);
        } elseif ($period == 'sd_bulan_lalu') {
            $jlbrg_sdblnlalu_debit->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_lalu]);
        }

        $jlbrg_sdblnlalu_debit->where('kd_rekening_debit', $idrekening);


        $jurnal_debit = $jlbrg_sdblnlalu_debit->first();

        $jurnal_db = $jurnal_debit->ttl != null ? $jurnal_debit->ttl : 0;
        $jurnal_kd = $jurnal_kredit->ttl != null ? $jurnal_kredit->ttl : 0;
        $jurnal = 0;
        if ($pb == 'pendapatan') {
            $jurnal = $jurnal_kd - $jurnal_db;
        } elseif ($pb == 'biaya') {
            $jurnal = $jurnal_db - $jurnal_kd;
        }

        $txlbrg_sdblnlalu_kredit = TransactionPayment::selectRaw('SUM(amount) as ttl')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $txlbrg_sdblnlalu_kredit->where('paid_on', '>=', $awal_tahun)
                ->where('paid_on', '<', $awal_hari);
        } elseif ($period == 'sd_bulan_lalu') {
            $txlbrg_sdblnlalu_kredit->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_lalu]);
        }

        $txlbrg_sdblnlalu_kredit->where('id_rekening_kredit', $idrekening);

        $transaction_kredit = $txlbrg_sdblnlalu_kredit->first();

        $txlbrg_sdblnlalu_debit = TransactionPayment::selectRaw('SUM(amount) as ttl')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $txlbrg_sdblnlalu_debit->where('paid_on', '>=', $awal_tahun)
                ->where('paid_on', '<', $awal_hari);
        } elseif ($period == 'sd_bulan_lalu') {
            $txlbrg_sdblnlalu_debit->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_lalu]);
        }

        $txlbrg_sdblnlalu_debit->where('id_rekening_debit', $idrekening);

        $transaction_debit = $txlbrg_sdblnlalu_debit->first();

        $transaction_db = $transaction_debit->ttl != null ? $transaction_debit->ttl : 0;
        $transaction_kd = $transaction_kredit->ttl != null ? $transaction_kredit->ttl : 0;
        $transaction = 0;
        if ($pb == 'pendapatan') {
            $transaction = $transaction_kd - $transaction_db;
        } elseif ($pb == 'biaya') {
            $transaction = $transaction_db - $transaction_kd;
        }

        $ajdlbrg_blnlalu_kredit = StockAdjustmentLine::selectRaw('SUM(quantity * unit_price) as ttl')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $ajdlbrg_blnlalu_kredit->where('transactions.created_at', '>=', $awal_tahun)
                ->where('transactions.created_at', '<', $awal_hari);
        } elseif ($period == 'sd_bulan_lalu') {
            $ajdlbrg_blnlalu_kredit->whereBetween('transactions.created_at', [$awal_tahun, $akhir_bulan_lalu]);
        }

        $ajdlbrg_blnlalu_kredit->where('id_rekening_kredit', $idrekening);

        $adjustment_kredit = $ajdlbrg_blnlalu_kredit->first();

        $ajdlbrg_blnlalu_debit = StockAdjustmentLine::selectRaw('SUM(quantity * unit_price) as ttl')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $ajdlbrg_blnlalu_debit->where('transactions.created_at', '>=', $awal_tahun)
                ->where('transactions.created_at', '<', $awal_hari);
        } elseif ($period == 'sd_bulan_lalu') {
            $ajdlbrg_blnlalu_debit->whereBetween('transactions.created_at', [$awal_tahun, $akhir_bulan_lalu]);
        }

        $ajdlbrg_blnlalu_debit->where('id_rekening_debit', $idrekening);


        $adjustment_debit = $ajdlbrg_blnlalu_debit->first();

        $adjustment_db = $adjustment_debit->ttl != null ? $adjustment_debit->ttl : 0;
        $adjustment_kd = $adjustment_kredit->ttl != null ? $adjustment_kredit->ttl : 0;
        $adjustment = 0;
        if ($pb == 'pendapatan') {
            $adjustment = $adjustment_kd - $adjustment_db;
        } elseif ($pb == 'biaya') {
            $adjustment = $adjustment_db - $adjustment_kd;
        }

        $fjurnal = $jurnal;
        $ftransaction = $transaction;
        $fadjustment = $adjustment;

        return $fjurnal + $ftransaction + $fadjustment;
    }


    public function LbRgBlnIni($idrekening, $pb, $tgl = null, $bln = null, $thn = null, $period = null)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        $awal_tahun       = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();

        $tgl_kondisi = date("$thn-$bln-01");
        $tgl_input = date("$thn-$bln-$tgl");
        $awal_hari = $tgl != null ? Carbon::createFromFormat('Y-m-d', $tgl_input)->startOfDay() : "";
        $akhir_hari = $tgl != null ? Carbon::createFromFormat('Y-m-d', $tgl_input)->endOfDay() : "";
        // $hari_minus = date('Y-m-d', strtotime('-1 days', strtotime($tgl_input)));
        // $hari_awal = $tgl !=null ? Carbon::createFromFormat('Y-m-d', $hari_minus)->endOfDay() : "";
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_ini = $bln != null ? Carbon::createFromFormat('Y-m-d', $tgl_kondisi)->endOfMonth() : "";
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";

        $jlbrg_blnini_kredit = Jurnal::selectRaw('SUM(nominal) as ttl')
            ->where('business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $jlbrg_blnini_kredit->whereBetween('tanggal_jurnal', [$awal_hari, $akhir_hari]);
            // ->where('tanggal_jurnal','<',$akhir_hari);
        } elseif ($period == 'bulan_ini') {
            $jlbrg_blnini_kredit->whereMonth('tanggal_jurnal', $m_now);
        }
        $jlbrg_blnini_kredit->whereYear('tanggal_jurnal', $y_now);

        $jlbrg_blnini_kredit->where('kd_rekening_kredit', $idrekening);

        $jurnal_kredit = $jlbrg_blnini_kredit->first();

        $jlbrg_blnini_debit = Jurnal::selectRaw('SUM(nominal) as ttl')
            ->where('business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $jlbrg_blnini_debit->whereBetween('tanggal_jurnal', [$awal_hari, $akhir_hari]);
            // ->where('tanggal_jurnal','<',$akhir_hari);
        } elseif ($period == 'bulan_ini') {
            $jlbrg_blnini_debit->whereMonth('tanggal_jurnal', $m_now);
        }
        $jlbrg_blnini_debit->whereYear('tanggal_jurnal', $y_now);

        $jlbrg_blnini_debit->where('kd_rekening_debit', $idrekening);

        $jurnal_debit = $jlbrg_blnini_debit->first();

        $jurnal_db = $jurnal_debit->ttl != null ? $jurnal_debit->ttl : 0;
        $jurnal_kd = $jurnal_kredit->ttl != null ? $jurnal_kredit->ttl : 0;
        $jurnal = 0;
        if ($pb == 'pendapatan') {
            $jurnal = $jurnal_kd - $jurnal_db;
        } elseif ($pb == 'biaya') {
            $jurnal = $jurnal_db - $jurnal_kd;
        }

        $txlbrg_blnini_kredit = TransactionPayment::selectRaw('SUM(amount) as ttl')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $txlbrg_blnini_kredit->whereBetween('paid_on', [$awal_hari, $akhir_hari]);
        } elseif ($period == 'bulan_ini') {
            $txlbrg_blnini_kredit->whereMonth('paid_on', $m_now);
        }
        $txlbrg_blnini_kredit->whereYear('paid_on', $y_now);

        $txlbrg_blnini_kredit->where('id_rekening_kredit', $idrekening);

        $transaction_kredit = $txlbrg_blnini_kredit->first();

        $txlbrg_blnini_debit = TransactionPayment::selectRaw('SUM(amount) as ttl')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $txlbrg_blnini_debit->whereBetween('paid_on', [$awal_hari, $akhir_hari]);
        } elseif ($period == 'bulan_ini') {
            $txlbrg_blnini_debit->whereMonth('paid_on', $m_now);
        }
        $txlbrg_blnini_debit->whereYear('paid_on', $y_now);

        $txlbrg_blnini_debit->where('id_rekening_debit', $idrekening);

        $transaction_debit = $txlbrg_blnini_debit->first();

        $transaction_db = $transaction_debit->ttl != null ? $transaction_debit->ttl : 0;
        $transaction_kd = $transaction_kredit->ttl != null ? $transaction_kredit->ttl : 0;
        $transaction = 0;

        if ($pb == 'pendapatan') {
            $transaction = $transaction_kd - $transaction_db;
        } elseif ($pb == 'biaya') {
            $transaction = $transaction_db - $transaction_kd;
        }

        $adjlbrg_blnini_kredit = StockAdjustmentLine::selectRaw('SUM(quantity * unit_price) as ttl')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $adjlbrg_blnini_kredit->whereBetween('transactions.created_at', [$awal_hari, $akhir_hari]);
            // ->where('transactions.created_at','<',$akhir_hari);
        } elseif ($period == 'bulan_ini') {
            $adjlbrg_blnini_kredit->whereMonth('transactions.created_at', $m_now);
        }
        $adjlbrg_blnini_kredit->whereYear('transactions.created_at', $y_now);

        $adjlbrg_blnini_kredit->where('id_rekening_kredit', $idrekening);

        $adjustment_kredit = $adjlbrg_blnini_kredit->first();

        $adjlbrg_blnini_debit = StockAdjustmentLine::selectRaw('SUM(quantity * unit_price) as ttl')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $adjlbrg_blnini_debit->whereBetween('transactions.created_at', [$awal_hari, $akhir_hari]);
            // ->where('transactions.created_at','<',$akhir_hari);
        } elseif ($period == 'bulan_ini') {
            $adjlbrg_blnini_debit->whereMonth('transactions.created_at', $m_now);
        }
        $adjlbrg_blnini_debit->whereYear('transactions.created_at', $y_now);

        $adjlbrg_blnini_debit->where('id_rekening_debit', $idrekening);

        $adjustment_debit = $adjlbrg_blnini_debit->first();

        $adjustment_db = $adjustment_debit->ttl != null ? $adjustment_debit->ttl : 0;
        $adjustment_kd = $adjustment_kredit->ttl != null ? $adjustment_kredit->ttl : 0;
        $adjustment = 0;
        if ($pb == 'pendapatan') {
            $adjustment = $adjustment_kd - $adjustment_db;
        } elseif ($pb == 'biaya') {
            $adjustment = $adjustment_db - $adjustment_kd;
        }

        $fjurnal = $jurnal;
        $ftransaction = $transaction;
        $fadjustment = $adjustment;

        return $fjurnal + $ftransaction + $fadjustment;
    }


    public function LbRgSdBlnIni($idrekening, $pb, $tgl = null, $bln = null, $thn = null, $period = null)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        $awal_tahun       = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();

        $tgl_kondisi = date("$thn-$bln-01");
        $tgl_input = date("$thn-$bln-$tgl");
        $akhir_hari = $tgl != null ? Carbon::createFromFormat('Y-m-d', $tgl_input)->endOfDay() : "";
        // $hari_minus = date('Y-m-d', strtotime('-1 days', strtotime($tgl_input)));
        // $hari_awal = $tgl !=null ? Carbon::createFromFormat('Y-m-d', $hari_minus)->endOfDay() : "";
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_ini = $bln != null ? Carbon::createFromFormat('Y-m-d', $tgl_kondisi)->endOfMonth() : "";
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";

        $jlbrg_sdblnini_kredit = Jurnal::selectRaw('SUM(nominal) as ttl')
            ->where('business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $jlbrg_sdblnini_kredit->where('tanggal_jurnal', '>=', $awal_tahun)
                ->where('tanggal_jurnal', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_ini') {
            $jlbrg_sdblnini_kredit->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_ini]);
        }

        $jlbrg_sdblnini_kredit->where('kd_rekening_kredit', $idrekening);

        $jurnal_kredit = $jlbrg_sdblnini_kredit->first();

        $jlbrg_sdblnini_debit = Jurnal::selectRaw('SUM(nominal) as ttl')
            ->where('business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $jlbrg_sdblnini_debit->where('tanggal_jurnal', '>=', $awal_tahun)
                ->where('tanggal_jurnal', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_ini') {
            $jlbrg_sdblnini_debit->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_ini]);
        }

        $jlbrg_sdblnini_debit->where('kd_rekening_debit', $idrekening);

        $jurnal_debit = $jlbrg_sdblnini_debit->first();

        $jurnal_db = $jurnal_debit->ttl != null ? $jurnal_debit->ttl : 0;
        $jurnal_kd = $jurnal_kredit->ttl != null ? $jurnal_kredit->ttl : 0;
        $jurnal = 0;
        if ($pb == 'pendapatan') {
            $jurnal = $jurnal_kd - $jurnal_db;
        } elseif ($pb == 'biaya') {
            $jurnal = $jurnal_db - $jurnal_kd;
        }

        $txlbrg_sdblnini_kredit = TransactionPayment::selectRaw('SUM(amount) as ttl')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $txlbrg_sdblnini_kredit->where('paid_on', '>=', $awal_tahun)
                ->where('paid_on', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_ini') {
            $txlbrg_sdblnini_kredit->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_ini]);
        }

        $txlbrg_sdblnini_kredit->where('id_rekening_kredit', $idrekening);

        $transaction_kredit = $txlbrg_sdblnini_kredit->first();

        $txlbrg_sdblnini_debit = TransactionPayment::selectRaw('SUM(amount) as ttl')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $txlbrg_sdblnini_debit->where('paid_on', '>=', $awal_tahun)
                ->where('paid_on', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_ini') {
            $txlbrg_sdblnini_debit->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_ini]);
        }

        $txlbrg_sdblnini_debit->where('id_rekening_debit', $idrekening);

        $transaction_debit = $txlbrg_sdblnini_debit->first();

        $transaction_db = $transaction_debit->ttl != null ? $transaction_debit->ttl : 0;
        $transaction_kd = $transaction_kredit->ttl != null ? $transaction_kredit->ttl : 0;
        $transaction = 0;
        if ($pb == 'pendapatan') {
            $transaction = $transaction_kd - $transaction_db;
        } elseif ($pb == 'biaya') {
            $transaction = $transaction_db - $transaction_kd;
        }

        $ajdlbrg_blnini_kredit = StockAdjustmentLine::selectRaw('SUM(quantity * unit_price) as ttl')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $ajdlbrg_blnini_kredit->where('transactions.created_at', '>=', $awal_tahun)
                ->where('transactions.created_at', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_ini') {
            $ajdlbrg_blnini_kredit->whereBetween('transactions.created_at', [$awal_tahun, $akhir_bulan_ini]);
        }

        $ajdlbrg_blnini_kredit->where('id_rekening_kredit', $idrekening);

        $adjustment_kredit = $ajdlbrg_blnini_kredit->first();

        $ajdlbrg_blnini_debit = StockAdjustmentLine::selectRaw('SUM(quantity * unit_price) as ttl')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $ajdlbrg_blnini_debit->where('transactions.created_at', '>=', $awal_tahun)
                ->where('transactions.created_at', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_ini') {
            $ajdlbrg_blnini_debit->whereBetween('transactions.created_at', [$awal_tahun, $akhir_bulan_ini]);
        }

        $ajdlbrg_blnini_debit->where('id_rekening_debit', $idrekening);

        $adjustment_debit = $ajdlbrg_blnini_debit->first();

        $adjustment_db = $adjustment_debit->ttl != null ? $adjustment_debit->ttl : 0;
        $adjustment_kd = $adjustment_kredit->ttl != null ? $adjustment_kredit->ttl : 0;
        $adjustment = 0;
        if ($pb == 'pendapatan') {
            $adjustment = $adjustment_kd - $adjustment_db;
        } elseif ($pb == 'biaya') {
            $adjustment = $adjustment_db - $adjustment_kd;
        }

        $fjurnal = $jurnal;
        $ftransaction = $transaction;
        $fadjustment = $adjustment;

        return $fjurnal + $ftransaction + $fadjustment;
    }


    public function getValueLabaRugiByIdRekening($idrekening, $pb, $tgl = null, $bln = null, $thn = null, $period = null)
    {
        $m_now            = $bln != null ? $bln : date('m');
        $y_now            = $thn != null ? $thn : date('Y');
        $awal_tahun       = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        // $akhir_bulan_lalu = $bln!=null ? Carbon::createFromFormat('Y-m', $thn."-".$bln)->subMonth(1)->endOfMonth() : "";
        // $akhir_bulan_ini  = $bln!=null ? Carbon::createFromFormat('Y-m', $thn."-".$bln)->endOfMonth() : "";
        $tgl_kondisi = date("$thn-$bln-01");
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_ini = $bln != null ? Carbon::createFromFormat('Y-m-d', $tgl_kondisi)->endOfMonth() : "";
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";

        $getjurnal = Jurnal::selectRaw('SUM(nominal) as ttl')
            ->where('business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $getjurnal->where('tanggal_jurnal', $thn . '-' . $bln . '-' . $tgl);
        }
        if ($period == 'bulan_ini') {
            $getjurnal->whereMonth('tanggal_jurnal', $m_now);
        } elseif ($period == 'sd_bulan_lalu') {
            //1 januari tahun ini - akhir bulan lalu
            $getjurnal->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_lalu]);
        } elseif ($period == 'sd_bulan_ini') {
            //1 januari tahun ini sampai akhir bulan ini
            $getjurnal->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_ini]);
        }

        $getjurnal->whereYear('tanggal_jurnal', $thn);


        if ($pb == 'pendapatan') {
            $getjurnal->where('kd_rekening_kredit', $idrekening);
        } elseif ($pb == 'biaya') {
            $getjurnal->where('kd_rekening_debit', $idrekening);
        }

        $jurnal = $getjurnal->first();

        $gettransaction = TransactionPayment::selectRaw('SUM(amount) as ttl')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $gettransaction->where('paid_on', $thn . '-' . $bln . '-' . $tgl);
        }
        if ($period == 'bulan_ini') {
            $gettransaction->whereMonth('paid_on', $m_now);
        } elseif ($period == 'sd_bulan_lalu') {
            //1 januari tahun ini - akhir bulan lalu
            $gettransaction->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_lalu]);
        } elseif ($period == 'sd_bulan_ini') {
            //1 januari tahun ini sampai akhir bulan ini
            $gettransaction->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_ini]);
        }

        $gettransaction->whereYear('paid_on', $thn);


        if ($pb == 'pendapatan') {
            $gettransaction->where('id_rekening_kredit', $idrekening);
        } elseif ($pb == 'biaya') {
            $gettransaction->where('id_rekening_debit', $idrekening);
        }

        $transaction = $gettransaction->first();

        $stockadjustment = StockAdjustmentLine::selectRaw('SUM(quantity * unit_price) as ttl')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->where('transactions.business_id', auth()->user()->business_id);

        if ($tgl != null) {
            $stockadjustment->where('transactions.created_at', $thn . '-' . $bln . '-' . $tgl);
        }
        if ($period == 'bulan_ini') {
            $stockadjustment->whereMonth('transactions.created_at', $m_now);
        } elseif ($period == 'sd_bulan_lalu') {
            //1 januari tahun ini - akhir bulan lalu
            $stockadjustment->whereBetween('transactions.created_at', [$awal_tahun, $akhir_bulan_lalu]);
        } elseif ($period == 'sd_bulan_ini') {
            //1 januari tahun ini sampai akhir bulan ini
            $stockadjustment->whereBetween('transactions.created_at', [$awal_tahun, $akhir_bulan_ini]);
        }

        $stockadjustment->whereYear('transactions.created_at', $thn);


        if ($pb == 'pendapatan') {
            $stockadjustment->where('id_rekening_kredit', $idrekening);
        } elseif ($pb == 'biaya') {
            $stockadjustment->where('id_rekening_debit', $idrekening);
        }

        $adjustment = $stockadjustment->first();

        $fjurnal = $jurnal->ttl != null ? $jurnal->ttl : 0;
        $ftransaction = $transaction->ttl != null ? $transaction->ttl : 0;
        $fadjustment = $adjustment->ttl != null ? $adjustment->ttl : 0;

        return $fjurnal + $ftransaction + $fadjustment;
    }

    public function getTransaksiBukuBesar($dk, $kd_rekening, $nama_rekening, $kd_buku = null, $tgl = null, $bln = null, $thn = null, $period = null)
    {
        $m_now = $bln != null ? $bln : date('m');
        $y_now = $thn != null ? $thn : date('Y');
        // $awal_tahun = "$thn-01-01";
        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        $tgl_kondisi = date("$thn-$bln-01");
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_ini = $bln != null ? Carbon::createFromFormat('Y-m-d', $tgl_kondisi)->endOfMonth() : "";
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";
        // $awal_tahun       = $thn!=null ?  Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear() : Carbon::now()->startOfYear();
        // $akhir_bulan_ini  = $bln!=null ? Carbon::createFromFormat('m', $bln)->endOfMonth() : Carbon::now()->endOfMonth();

        $getjurnal = Jurnal::selectRaw('jurnal.id,tanggal_jurnal as tanggal,keterangan,nominal,ref_id,kd_rekening_' . $dk . ',jurnal.created_at,"' . $nama_rekening . '" as nama_rekening,"1" as urutan,users.initial,"-" as id_kontak,"-" as nama_kontak,"-" as invoice_no')
            ->leftJoin('users', 'jurnal.created_by', '=', 'users.id')
            ->where('jurnal.business_id', auth()->user()->business_id)
            ->where('kd_rekening_' . $dk, $kd_rekening);

        if ($tgl != null) {
            $getjurnal->whereDate('tanggal_jurnal', $thn . '-' . $bln . '-' . $tgl);
        }
        if ($period == 'bulan_ini') {
            $getjurnal->whereMonth('tanggal_jurnal', $m_now);
            $getjurnal->whereYear('tanggal_jurnal', $y_now);
        } elseif ($period == 'sd_bulan_ini') {
            //1 januari tahun ini sampai akhir bulan ini
            $getjurnal->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_ini]);
            // $getjurnal->whereYear('tanggal_jurnal',$y_now);
        } elseif ($period == 'sd_bulan_lalu') {
            //1 januari tahun ini - akhir bulan lalu
            $getjurnal->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_lalu]);
            // $getjurnal->whereYear('tanggal_jurnal',$y_now);
        } elseif ($period == 'komulatif') {
            //sepanjang tahun
            $getjurnal->whereYear('tanggal_jurnal', $thn);
        }

        $getjurnal->orderBy('tanggal_jurnal', 'ASC');
        $jurnal    = $getjurnal->get()->toArray();

        $gettransaksi = Transaction::selectRaw('transactions.id,transaction_date as tanggal,additional_notes as keterangan,(final_total - shipping_charges) as nominal,ref_no as ref_id,kd_rekening_' . $dk . ',transactions.created_at,"' . $nama_rekening . '" as nama_rekening,"2" as urutan,users.initial,contacts.contact_id as id_kontak,contacts.name as nama_kontak,invoice_no')
            ->leftJoin('users', 'transactions.created_by', '=', 'users.id')
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->where('kd_rekening_' . $dk, $kd_rekening)
            // ->where('transactions.payment_status',['paid'])
        ;
        if ($tgl != null) {
            $gettransaksi->whereDate('transaction_date', $thn . '-' . $bln . '-' . $tgl);
        }
        if ($period == 'bulan_ini') {
            $gettransaksi->whereMonth('transaction_date', $m_now);
            $gettransaksi->whereYear('transaction_date', $y_now);
        } elseif ($period == 'sd_bulan_ini') {
            //1 januari tahun ini sampai akhir bulan ini
            $gettransaksi->whereBetween('transaction_date', [$awal_tahun, $akhir_bulan_ini]);
            // $gettransaksi->whereYear('transaction_date',$y_now);
        } elseif ($period == 'sd_bulan_lalu') {
            //1 januari tahun ini - akhir bulan lalu
            $gettransaksi->whereBetween('transaction_date', [$awal_tahun, $akhir_bulan_lalu]);
            // $gettransaksi->whereYear('transaction_date',$y_now);
        } elseif ($period == 'komulatif') {
            //sepanjang tahun
            $gettransaksi->whereYear('transaction_date', $thn);
        }

        $gettransaksi->orderBy('transaction_date', 'ASC');
        $transaksi = $gettransaksi->get()->toArray();

        $gethtgptgbiayakirim = Transaction::selectRaw('transactions.id,transaction_date as tanggal,additional_notes as keterangan,shipping_charges as nominal,ref_no as ref_id,kd_rekening_' . $dk . '_htg_biaya_kirim as kd_rekening_' . $dk . ',transactions.created_at,"' . $nama_rekening . '" as nama_rekening,"3" as urutan,users.initial,contacts.contact_id as id_kontak,contacts.name as nama_kontak,invoice_no')
            ->leftJoin('users', 'transactions.created_by', '=', 'users.id')
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->where('kd_rekening_' . $dk . '_htg_biaya_kirim', $kd_rekening);
        if ($tgl != null) {
            $gethtgptgbiayakirim->whereDate('transaction_date', $thn . '-' . $bln . '-' . $tgl);
        }
        if ($period == 'bulan_ini') {
            $gethtgptgbiayakirim->whereMonth('transaction_date', $m_now);
            $gethtgptgbiayakirim->whereYear('transaction_date', $y_now);
        } elseif ($period == 'sd_bulan_lalu') {
            //1 januari tahun ini - akhir bulan lalu
            $gethtgptgbiayakirim->whereBetween('transaction_date', [$awal_tahun, $akhir_bulan_lalu]);
            // $gethtgptgbiayakirim->whereYear('transaction_date',$y_now);
        } elseif ($period == 'sd_bulan_ini') {
            //1 januari tahun ini sampai akhir bulan ini
            $gethtgptgbiayakirim->whereBetween('transaction_date', [$awal_tahun, $akhir_bulan_ini]);
        } elseif ($period == 'komulatif') {
            //sepanjang tahun
            $gethtgptgbiayakirim->whereYear('transaction_date', $thn);
        }

        $gethtgptgbiayakirim->orderBy('transaction_date', 'ASC');
        $htgptgbiayakirim = $gethtgptgbiayakirim->get()->toArray();

        $gettransaksipayment = TransactionPayment::selectRaw('transactions.id as trxx,transaction_payments.id,paid_on as tanggal,note as keterangan,amount as nominal,transactions.ref_no as ref_id,id_rekening_' . $dk . ' as kd_rekening_' . $dk . ',transaction_payments.created_at,"' . $nama_rekening . '" as nama_rekening,"4" as urutan,users.initial,contacts.contact_id as id_kontak,contacts.name as nama_kontak,invoice_no')
            ->leftJoin('users', 'transaction_payments.created_by', '=', 'users.id')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id)
            ->where('id_rekening_' . $dk, $kd_rekening);
        if ($tgl != null) {
            $gettransaksipayment->whereDate('paid_on', $thn . '-' . $bln . '-' . $tgl);
        }
        if ($period == 'bulan_ini') {
            $gettransaksipayment->whereMonth('paid_on', $m_now);
            $gettransaksipayment->whereYear('paid_on', $y_now);
        } elseif ($period == 'sd_bulan_ini') {
            //1 januari tahun ini sampai akhir bulan ini
            $gettransaksipayment->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_ini]);
            // $gettransaksipayment->whereYear('paid_on',$y_now);
            // $gettransaksipayment->whereMonth('paid_on',$m_now);
            // $gettransaksipayment->whereYear('paid_on',$y_now);
        } elseif ($period == 'sd_bulan_lalu') {
            //1 januari tahun ini - akhir bulan lalu
            $gettransaksipayment->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_lalu]);
            // $gettransaksipayment->whereYear('paid_on',$y_now);
        } elseif ($period == 'komulatif') {
            //sepanjang tahun
            $gettransaksipayment->whereYear('paid_on', $thn);
        }

        $gettransaksipayment->orderBy('paid_on', 'ASC');
        $transaksipayment = $gettransaksipayment->get()->toArray();

        $getstockadjustment = StockAdjustmentLine::selectRaw('stock_adjustment_lines.id,stock_adjustment_lines.created_at as tanggal,transactions.staff_note as keterangan,(unit_price * quantity) as nominal,lot_no_line_id as ref_id,id_rekening_' . $dk . ' as kd_rekening_' . $dk . ',transactions.created_at,"' . $nama_rekening . '" as nama_rekening,"6" as urutan,users.initial,contacts.contact_id as id_kontak,contacts.name as nama_kontak,transactions.invoice_no')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->leftJoin('users', 'transactions.created_by', '=', 'users.id')
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->where('id_rekening_' . $dk, $kd_rekening);
        if ($tgl != null) {
            $getstockadjustment->whereDate('stock_adjustment_lines.created_at', $thn . '-' . $bln . '-' . $tgl);
        }
        if ($period == 'bulan_ini') {
            $getstockadjustment->whereMonth('stock_adjustment_lines.created_at', $m_now);
            $getstockadjustment->whereYear('stock_adjustment_lines.created_at', $y_now);
        } elseif ($period == 'sd_bulan_ini') {
            //1 januari tahun ini sampai akhir bulan ini
            $getstockadjustment->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_bulan_ini]);
            $getstockadjustment->whereYear('stock_adjustment_lines.created_at', $y_now);
        } elseif ($period == 'sd_bulan_lalu') {
            //1 januari tahun ini - akhir bulan lalu
            $getstockadjustment->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_bulan_lalu]);
            $getstockadjustment->whereYear('stock_adjustment_lines.created_at', $y_now);
        } elseif ($period == 'komulatif') {
            //sepanjang tahun
            $getstockadjustment->whereYear('stock_adjustment_lines.created_at', $thn);
        }

        $getstockadjustment->orderBy('stock_adjustment_lines.created_at', 'ASC');
        $stockadjustment = $getstockadjustment->get()->toArray();

        $total = array_merge($jurnal, $transaksi, $htgptgbiayakirim, $transaksipayment, $stockadjustment);
        return $total;
    }


    public function getSdBulanIni($dk, $kd_rekening, $nama_rekening, $kd_buku = null, $tgl = null, $bln = null, $thn = null, $period = null)
    {

        $m_now = $bln != null ? $bln : date('m');
        $y_now = $thn != null ? $thn : date('Y');
        // $awal_tahun = "$thn-01-01";
        $kd_jb = explode('.', $kd_rekening);
        if (NeracaAwalBulan::where('business_id', auth()->user()->business_id)->where('tanggal', "$thn-$bln-01")->where('kd_jb', $kd_jb[0])->count() >= 1) {
            $awal_tahun = Carbon::create($thn, $bln, 31, 12, 0, 0)->startOfMonth();
        } else {
            $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        }
        $tgl_kondisi = date("$thn-$bln-01");
        $tgl_input = date("$thn-$bln-$tgl");
        $akhir_hari = $tgl != null ? Carbon::createFromFormat('Y-m-d', $tgl_input)->endOfDay() : "";
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_ini = $bln != null ? Carbon::createFromFormat('Y-m-d', $tgl_kondisi)->endOfMonth() : "";
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";


        $j_sd_bulan_ini = Jurnal::selectRaw('jurnal.id,tanggal_jurnal as tanggal,keterangan,nominal,ref_id,kd_rekening_' . $dk . ',jurnal.created_at,"' . $nama_rekening . '" as nama_rekening,"1" as urutan,users.initial,"-" as id_kontak,"-" as nama_kontak,"-" as invoice_no')
            ->leftJoin('users', 'jurnal.created_by', '=', 'users.id')
            ->where('jurnal.business_id', auth()->user()->business_id)
            ->where('kd_rekening_' . $dk, $kd_rekening);

        if ($tgl != null) {
            $j_sd_bulan_ini->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_hari]);
        } elseif ($period == 'sd_bulan_ini') {
            //1 januari tahun ini - akhir bulan lalu
            $j_sd_bulan_ini->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_ini]);
            // $j_sd_bulan_ini->whereYear('tanggal_jurnal',$y_now);
        }

        $j_sd_bulan_ini->orderBy('tanggal_jurnal', 'ASC');
        $jsdblnini   = $j_sd_bulan_ini->get()->toArray();

        $tr_sd_bln_ini = Transaction::selectRaw('transactions.id,transaction_date as tanggal,additional_notes as keterangan,(final_total - shipping_charges) as nominal,ref_no as ref_id,kd_rekening_' . $dk . ',transactions.created_at,"' . $nama_rekening . '" as nama_rekening,"2" as urutan,users.initial,contacts.contact_id as id_kontak,contacts.name as nama_kontak,invoice_no')
            ->leftJoin('users', 'transactions.created_by', '=', 'users.id')
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->where('kd_rekening_' . $dk, $kd_rekening);

        if ($tgl != null) {
            $tr_sd_bln_ini->whereBetween('transaction_date', [$awal_tahun, $akhir_hari]);
        } elseif ($period == 'sd_bulan_ini') {
            //1 januari tahun ini - akhir bulan lalu
            $tr_sd_bln_ini->whereBetween('transaction_date', [$awal_tahun, $akhir_bulan_ini]);
            // $tr_sab->whereYear('transaction_date',$y_now);
        }
        $tr_sd_bln_ini->orderBy('transaction_date', 'ASC');
        $trsdblnini   = $tr_sd_bln_ini->get()->toArray();


        $payment_sd_bln_ini = TransactionPayment::selectRaw('transactions.id as trxx,transaction_payments.id,paid_on as tanggal,note as keterangan,amount as nominal,payment_ref_no as ref_id,id_rekening_' . $dk . ' as kd_rekening_' . $dk . ',transaction_payments.created_at,"' . $nama_rekening . '" as nama_rekening,"4" as urutan,users.initial,contacts.contact_id as id_kontak,contacts.name as nama_kontak,invoice_no')
            ->leftJoin('users', 'transaction_payments.created_by', '=', 'users.id')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id)
            ->where('id_rekening_' . $dk, $kd_rekening);

        if ($tgl != null) {
            $payment_sd_bln_ini->whereBetween('paid_on', [$awal_tahun, $akhir_hari]);
        } elseif ($period == 'sd_bulan_ini') {
            //1 januari tahun ini - akhir bulan lalu
            $payment_sd_bln_ini->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_ini]);
            // $payment_sab->whereYear('paid_on',$y_now);
        }

        $payment_sd_bln_ini->orderBy('paid_on', 'ASC');
        $psdblnini   = $payment_sd_bln_ini->get()->toArray();

        $so_sd_bln_ini = StockAdjustmentLine::selectRaw('stock_adjustment_lines.id,stock_adjustment_lines.created_at as tanggal,transactions.staff_note as keterangan,(unit_price * quantity) as nominal,lot_no_line_id as ref_id,id_rekening_' . $dk . ' as kd_rekening_' . $dk . ',transactions.created_at,"' . $nama_rekening . '" as nama_rekening,"6" as urutan,users.initial,contacts.contact_id as id_kontak,contacts.name as nama_kontak,transactions.invoice_no')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->leftJoin('users', 'transactions.created_by', '=', 'users.id')
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->where('id_rekening_' . $dk, $kd_rekening);
        if ($tgl != null) {
            $so_sd_bln_ini->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_hari]);
        } elseif ($period == 'sd_bulan_ini') {
            //1 januari tahun ini - akhir bulan lalu
            $so_sd_bln_ini->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_bulan_ini]);
            // $so_sd_bln_ini->whereYear('stock_adjustment_lines.created_at',$y_now);
        }

        $so_sd_bln_ini->orderBy('stock_adjustment_lines.created_at', 'ASC');
        $sosdblnini   = $so_sd_bln_ini->get()->toArray();

        $total = array_merge($jsdblnini, $trsdblnini, $psdblnini, $sosdblnini);
        // $total = $paydua;

        return $total;
    }


    public function getSaldoAwalBulan($dk, $kd_rekening, $nama_rekening, $kd_buku = null, $tgl = null, $bln = null, $thn = null, $period = null)
    {
        $m_now = $bln != null ? $bln : date('m');
        $y_now = $thn != null ? $thn : date('Y');
        // $awal_tahun = "$thn-01-01";
        $awal_tahun = Carbon::create($thn, 1, 31, 12, 0, 0)->startOfYear();
        $tgl_kondisi = date("$thn-$bln-01");
        $tgl_input = date("$thn-$bln-$tgl");
        $akhir_hari = $tgl != null ? Carbon::createFromFormat('Y-m-t', $tgl_input)->startOfDay() : "";
        $minus_satu = date('Y-m-d', strtotime('-1 months', strtotime($tgl_kondisi)));
        $akhir_bulan_ini = $bln != null ? Carbon::createFromFormat('Y-m-d', $tgl_kondisi)->endOfMonth() : "";
        $akhir_bulan_lalu = $bln != null ? Carbon::createFromFormat('Y-m-d', $minus_satu)->endOfMonth() : "";

        $jurnal_sab = Jurnal::selectRaw('jurnal.id,tanggal_jurnal as tanggal,keterangan,nominal,ref_id,kd_rekening_' . $dk . ',jurnal.created_at,"' . $nama_rekening . '" as nama_rekening,"1" as urutan,users.initial,"-" as id_kontak,"-" as nama_kontak,"-" as invoice_no')
            ->leftJoin('users', 'jurnal.created_by', '=', 'users.id')
            ->where('jurnal.business_id', auth()->user()->business_id)
            ->where('kd_rekening_' . $dk, $kd_rekening);

        if ($tgl != null) {
            $jurnal_sab->where('tanggal_jurnal', '>=', $awal_tahun)
                ->where('tanggal_jurnal', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_lalu') {
            //1 januari tahun ini - akhir bulan lalu
            $jurnal_sab->whereBetween('tanggal_jurnal', [$awal_tahun, $akhir_bulan_lalu]);
            // $jurnal_sab->whereYear('tanggal_jurnal',$y_now);
        }

        $jurnal_sab->orderBy('tanggal_jurnal', 'ASC');
        $jsab   = $jurnal_sab->get()->toArray();

        $tr_sab = Transaction::selectRaw('transactions.id,transaction_date as tanggal,additional_notes as keterangan,(final_total - shipping_charges) as nominal,ref_no as ref_id,kd_rekening_' . $dk . ',transactions.created_at,"' . $nama_rekening . '" as nama_rekening,"2" as urutan,users.initial,contacts.contact_id as id_kontak,contacts.name as nama_kontak,invoice_no')
            ->leftJoin('users', 'transactions.created_by', '=', 'users.id')
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->where('kd_rekening_' . $dk, $kd_rekening);

        if ($tgl != null) {
            $tr_sab->where('transaction_date', '>=', $awal_tahun)
                ->where('transaction_date', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_lalu') {
            //1 januari tahun ini - akhir bulan lalu
            $tr_sab->whereBetween('transaction_date', [$awal_tahun, $akhir_bulan_lalu]);
            // $tr_sab->whereYear('transaction_date',$y_now);
        }
        $tr_sab->orderBy('transaction_date', 'ASC');
        $trsab   = $tr_sab->get()->toArray();

        $tr_ht_sab = Transaction::selectRaw('transactions.id,transaction_date as tanggal,additional_notes as keterangan,shipping_charges as nominal,ref_no as ref_id,kd_rekening_' . $dk . '_htg_biaya_kirim as kd_rekening_' . $dk . ',transactions.created_at,"' . $nama_rekening . '" as nama_rekening,"3" as urutan,users.initial,contacts.contact_id as id_kontak,contacts.name as nama_kontak,invoice_no')
            ->leftJoin('users', 'transactions.created_by', '=', 'users.id')
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->where('kd_rekening_' . $dk . '_htg_biaya_kirim', $kd_rekening);

        if ($tgl != null) {
            $tr_ht_sab->where('transaction_date', '>=', $awal_tahun)
                ->where('transaction_date', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_lalu') {
            //1 januari tahun ini - akhir bulan lalu
            $tr_ht_sab->whereBetween('transaction_date', [$awal_tahun, $akhir_bulan_lalu]);
            // $tr_sab->whereYear('transaction_date',$y_now);
        }

        $tr_ht_sab->orderBy('transaction_date', 'ASC');
        $trhtsab = $tr_ht_sab->get()->toArray();

        $payment_sab = TransactionPayment::selectRaw('transactions.id as trxx,transaction_payments.id,paid_on as tanggal,note as keterangan,amount as nominal,payment_ref_no as ref_id,id_rekening_' . $dk . ' as kd_rekening_' . $dk . ',transaction_payments.created_at,"' . $nama_rekening . '" as nama_rekening,"4" as urutan,users.initial,contacts.contact_id as id_kontak,contacts.name as nama_kontak,invoice_no')
            ->leftJoin('users', 'transaction_payments.created_by', '=', 'users.id')
            ->join('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transaction_payments.business_id', auth()->user()->business_id)
            ->where('id_rekening_' . $dk, $kd_rekening);

        if ($tgl != null) {
            $payment_sab->where('paid_on', '>=', $awal_tahun)
                ->where('paid_on', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_lalu') {
            //1 januari tahun ini - akhir bulan lalu
            $payment_sab->whereBetween('paid_on', [$awal_tahun, $akhir_bulan_lalu]);
            // $payment_sab->whereYear('paid_on',$y_now);
        }

        $payment_sab->orderBy('paid_on', 'ASC');
        $psab   = $payment_sab->get()->toArray();

        $adjustment_sab = StockAdjustmentLine::selectRaw('stock_adjustment_lines.id,stock_adjustment_lines.created_at as tanggal,transactions.staff_note as keterangan,(unit_price * quantity) as nominal,lot_no_line_id as ref_id,id_rekening_' . $dk . ' as kd_rekening_' . $dk . ',transactions.created_at,"' . $nama_rekening . '" as nama_rekening,"6" as urutan,users.initial,contacts.contact_id as id_kontak,contacts.name as nama_kontak,transactions.invoice_no')
            ->join('transactions', 'stock_adjustment_lines.transaction_id', '=', 'transactions.id')
            ->leftJoin('users', 'transactions.created_by', '=', 'users.id')
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transactions.business_id', auth()->user()->business_id)
            ->where('id_rekening_' . $dk, $kd_rekening);

        if ($tgl != null) {
            $adjustment_sab->where('stock_adjustment_lines.created_at', '>=', $awal_tahun)
                ->where('stock_adjustment_lines.created_at', '<', $akhir_hari);
        } elseif ($period == 'sd_bulan_lalu') {
            //1 januari tahun ini - akhir bulan lalu
            $adjustment_sab->whereBetween('stock_adjustment_lines.created_at', [$awal_tahun, $akhir_bulan_lalu]);
            // $adjustment_sab->whereYear('stock_adjustment_lines.created_at',$y_now);
        }

        $adjustment_sab->orderBy('stock_adjustment_lines.created_at', 'ASC');
        $sosab   = $adjustment_sab->get()->toArray();

        $total = array_merge($jsab, $trsab, $trhtsab, $psab, $sosab);
        // $total = $paydua;

        return $total;
    }
}
