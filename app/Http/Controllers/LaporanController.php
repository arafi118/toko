<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use App\User;
use Illuminate\Http\Request;
use App\JenisBuku;
use App\Inventaris;
use App\Business;
use App\BusinessLocation;
use App\RekeningRiil;
use App\Rekening;
use App\Exports\NeracaExport;
use App\NeracaAwalBulan;
use PDF;
use Excel;

class LaporanController extends Controller
{
    public function index()
    {
        $jenisbuku = JenisBuku::where('business_id', auth()->user()->business_id)->get();
        $rekening_rill = RekeningRiil::whereNotIn('posisi', [4, 5])->get();

        return view('laporan.index', ['jenisbuku' => $jenisbuku, 'rill' => $rekening_rill]);
    }

    public function tutup()
    {
        $rekening_rill = RekeningRiil::whereNotIn('posisi', [4, 5])->get();
        return view('laporan.tutup_saldo', ['rill' => $rekening_rill]);
    }

    public function saldo_neraca(Request $req)
    {
        $jenis_buku   = new JenisBuku;

        $tgl        = $req->tgl;
        $bln        = $req->bln;
        $thn        = $req->thn;

        if ($bln - 1 < 1) {
            $bln = 12;
            $thn -= 1;
        } else {
            $bln -= 1;
        }

        $tanggal    = date('Y-m-d', strtotime('+1 months', strtotime("$thn-$bln-01")));
        $r = RekeningRiil::where('idrekening', $req->id_rekening)->first();
        if ($r->posisi == '1') {
            $jenis = 'aktiva';
        } elseif ($r->posisi == '2' || $r->posisi == '3') {
            $jenis = 'passiva';
        } else {
            die;
        }
        foreach ($r->getRekening($r->idrekening) as $ch) {
            if (NeracaAwalBulan::where('kd_jb', $ch->kd_jb)->where('tanggal', $tanggal)->where('business_id', auth()->user()->business_id)->count() > 0) {
                $nc = NeracaAwalBulan::where('kd_jb', $ch->kd_jb)->where('tanggal', $tanggal)->where('business_id', auth()->user()->business_id)->first();
                NeracaAwalBulan::where('id', $nc->id)->delete();
            }

            $saldo_debit  = $jenis_buku->getSaldoAwal($ch->kd_jb, 'debit', ($bln == 12) ? $thn + 1 : $thn);
            $saldo_kredit = $jenis_buku->getSaldoAwal($ch->kd_jb, 'kredit', ($bln == 12) ? $thn + 1 : $thn);
            $awal_debit   = $saldo_debit;
            $awal_kredit  = $saldo_kredit;

            if ($jenis == 'aktiva') {
                $saldo_awal_tahun   = $awal_debit->ttltahunlalu - $awal_kredit->ttltahunlalu;
                $saldo_awal_pakai   = $awal_debit->ttlawalpakai - $awal_kredit->ttlawalpakai;
            } else {
                $saldo_awal_tahun   = $awal_kredit->ttltahunlalu - $awal_debit->ttltahunlalu;
                $saldo_awal_pakai   = $awal_kredit->ttlawalpakai - $awal_debit->ttlawalpakai;
            }

            $transaction_awl_bln            = $r->fromTransactionAwalBulan($jenis, $ch->kd_jb, $req->tgl, $req->bln, $req->thn, 'sd_bulan_lalu', true);
            $transaction_payment_awl_bln    = $r->fromTransactionPaymentAwalBulan($jenis, $ch->kd_jb, $req->tgl, $req->bln, $req->thn, 'sd_bulan_lalu', true);
            $jurnal_awl_bln                 = $r->fromJurnalAwalBulan($jenis, $ch->kd_jb, $req->tgl, $req->bln, $req->thn, 'sd_bulan_lalu', true);
            $shipping_charges_awl_bln       = $r->fromShippingChargesAwalBulan($jenis, $ch->kd_jb, $req->tgl, $req->bln, $req->thn, 'sd_bulan_lalu', true);
            $stock_adjustment_lines_awl_bln = $r->fromStockAdjustmentAwalBulan($jenis, $ch->kd_jb, $req->tgl, $req->bln, $req->thn, 'sd_bulan_lalu', true);
            $awal_bulan                     = $transaction_awl_bln['total'] + $transaction_payment_awl_bln['total'] + $jurnal_awl_bln['total'] + $shipping_charges_awl_bln['total'] + $stock_adjustment_lines_awl_bln['total'];
            $kredit_awal_bulan              = $transaction_awl_bln['kredit'] + $transaction_payment_awl_bln['kredit'] + $jurnal_awl_bln['kredit'] + $shipping_charges_awl_bln['kredit'] + $stock_adjustment_lines_awl_bln['kredit'];
            $debit_awal_bulan               = $transaction_awl_bln['debit'] + $transaction_payment_awl_bln['debit'] + $jurnal_awl_bln['debit'] + $shipping_charges_awl_bln['debit'] + $stock_adjustment_lines_awl_bln['debit'];

            $total          = $awal_bulan + $saldo_awal_tahun;
            $total_debit    = $debit_awal_bulan;
            $total_kredit   = $kredit_awal_bulan;
            $neraca = NeracaAwalBulan::create([
                'business_id'       => auth()->user()->business_id,
                'kd_jb'             => $ch->kd_jb,
                'tanggal'           => $tanggal,
                "awal_tahun"        => "$saldo_awal_tahun",
                "awal_tahun_debit"  => $awal_debit->ttltahunlalu,
                "awal_tahun_kredit" => $awal_kredit->ttltahunlalu,
                "awal_pakai"        => "$saldo_awal_pakai",
                "awal_pakai_debit"  => $awal_kredit->ttlawalpakai,
                "awal_pakai_kredit" => $awal_debit->ttlawalpakai,
                "total"             => "$total",
                "total_debit"       => "$total_debit",
                "total_kredit"      => "$total_kredit"
            ]);
        }

        return response()->json([
            'status'    => true,
            'msg'       => 'Tutup saldo bulanan rekening ' . $r->nama_rr . ' berhasil.'
        ]);
    }

    public function neraca(Request $req)
    {
        $business_id = auth()->user()->business_id;

        $jr = request()->jenis_rekening;
        $ra = RekeningRiil::where('posisi', 1)->get();
        $rp = RekeningRiil::whereNotIn('posisi', [1, 4, 5])->get();
        $bl = BusinessLocation::where('business_id', '=', $business_id)->first();
        $awal_bus = Business::where('id', '=', $business_id)->first();
        $nr = new RekeningRiil;
        $lr = $nr->getLabaRugi($req->tgl, $req->bln, $req->thn, $awal_bus->start_date);
        // dd($lr);
        if ($req->type == 'pdf') {
            $pdf = PDF::loadView('laporan.neraca.preview', ['bl' => $bl, 'rekening_aktiva' => $ra, 'rekening_pasiva' => $rp, 'laba_rugi' => $lr, 'business_id' => $business_id]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download('neraca.pdf');
        } elseif ($req->type == 'excel') {
            die('Under construction');
        } else {
            return view('laporan.neraca.' . $req->type, ['bl' => $bl, 'rekening_aktiva' => $ra, 'rekening_pasiva' => $rp, 'laba_rugi' => $lr, 'jr' => $jr, 'business_id' => $business_id]);
        }
    }

    public function laba_rugi(Request $req)
    {
        $business_id = auth()->user()->business_id;

        $bl = BusinessLocation::where('business_id', '=', $business_id)->first();
        $jb_pendapatan = JenisBuku::where('business_id', auth()->user()->business_id)->where('posisi', 4)->get();
        $jb_biaya = JenisBuku::where('business_id', auth()->user()->business_id)->where('posisi', 5)->get();

        if ($req->type == 'pdf') {
            ini_set('max_execution_time', 7200);
            $pdf = PDF::loadView('laporan.laba_rugi.preview', compact('bl', 'jb_pendapatan', 'jb_biaya'));

            return $pdf->download('rugi_laba.pdf');
        } else {
            return view('laporan.laba_rugi.' . $req->type, compact('bl', 'jb_pendapatan', 'jb_biaya'));
        }
    }

    public function buku_besar(Request $req)
    {
        $ins = $req->jenis_buku_besar;
        $business_id    = auth()->user()->business_id;
        $jenisbuku      = JenisBuku::where([['business_id', $business_id], ['ins', $ins]]);
        $bl             = BusinessLocation::selectRaw('id,business_id,location_id,name,landmark,country,state,city,zip_code')
            ->where('business_id', '=', $business_id)->first();

        $jenisbuku = $jenisbuku->first();
        $nama_buku      = $jenisbuku->nama_jb;
        $kode_buku      = $jenisbuku->kd_jb;

        if ($jenisbuku->posisi == '1' || $jenisbuku->posisi == '5') {
            $posisi = 'aktiva/biaya';
        } else {
            $posisi = 'passiva';
        }

        $reks           = Rekening::where('business_id', $business_id)->where('kd_jb', $kode_buku)->get();
        $trx = array();
        $sd_bln_ini_debit = 0;
        $sd_bln_ini_kredit = 0;
        $sd_bln_lalu_debit = 0;
        $sd_bln_lalu_kredit = 0;
        $th_ini_debit = 0;
        $th_ini_kredit = 0;

        $nc = NeracaAwalBulan::where('kd_jb', $jenisbuku->kd_jb)->where('tanggal', $req->thn . '-' . $req->bln . '-01')->where('business_id', auth()->user()->business_id);
        foreach ($reks as $key) {
            $jb = new JenisBuku;

            $get_trx = $jb->getTransaksiBukuBesar($key->jenis_mutasi, $key->kd_rekening, $key->nama_rekening, $kode_buku, $req->tgl, $req->bln, $req->thn, 'bulan_ini');
            if ($nc->count() <= 0) {
                $trx_sd_bln_ini = $jb->getSdBulanIni($key->jenis_mutasi, $key->kd_rekening, $key->nama_rekening, $kode_buku, $req->tgl, $req->bln, $req->thn, 'sd_bulan_ini');
                $trx_sd_bln_lalu = $jb->getSaldoAwalBulan($key->jenis_mutasi, $key->kd_rekening, $key->nama_rekening, $kode_buku, $req->tgl, $req->bln, $req->thn, 'sd_bulan_lalu');
            }
            $trx_th_ini = $jb->getTransaksiBukuBesar($key->jenis_mutasi, $key->kd_rekening, $key->nama_rekening, $kode_buku, $req->tgl, $req->bln, $req->thn, 'komulatif');

            if ($get_trx != null) {
                foreach ($get_trx as $ky => $vl) {
                    $trx[] = $vl;
                }
            }

            if ($nc->count() <= 0) {
                if ($trx_sd_bln_ini != null) {
                    foreach ($trx_sd_bln_ini as $ky => $vl) {
                        $sd_bln_ini_debit += isset($vl['kd_rekening_debit']) ? $vl['nominal'] : 0;
                        $sd_bln_ini_kredit += isset($vl['kd_rekening_kredit']) ? $vl['nominal'] : 0;
                    }
                }
                if ($trx_sd_bln_lalu != null) {
                    foreach ($trx_sd_bln_lalu as $ky => $vl) {
                        $sd_bln_lalu_debit += isset($vl['kd_rekening_debit']) ? $vl['nominal'] : 0;
                        $sd_bln_lalu_kredit += isset($vl['kd_rekening_kredit']) ? $vl['nominal'] : 0;
                    }
                }
            }

            if ($trx_th_ini != null) {
                foreach ($trx_th_ini as $ky => $vl) {
                    $th_ini_debit += isset($vl['kd_rekening_debit']) ? $vl['nominal'] : 0;
                    $th_ini_kredit += isset($vl['kd_rekening_kredit']) ? $vl['nominal'] : 0;
                }
            }
        }

        // $data['hsl'] = array();
        // foreach ($trx as $key => $value) {
        //     // $data['hsl'][strtotime($value['tanggal'])+strtotime($value['created_at'])+$value['id']] = $value; 
        //     // $data['hsl'][strtotime($value['tanggal'])+strtotime($value['created_at'])] = $value; 
        //     $data['hsl'][strtotime($value['created_at'])+$value['id']] = $value; 
        //     // $data['hsl'][strtotime($value['tanggal'])+$value['id']] = $value; 
        // }

        // ksort($data['hsl']);

        $data = $this->arraySort($trx);

        $awal_tahun_debit = 0;
        $awal_tahun_kredit = 0;
        $awal_pakai_debit = 0;
        $awal_pakai_kredit = 0;
        if ($nc->count() > 0) {
            $sd_bln_lalu_debit = $nc->first()->total_debit;
            $sd_bln_lalu_kredit = $nc->first()->total_kredit;
            $awal_tahun_debit = $nc->first()->awal_tahun_debit;
            $awal_tahun_kredit = $nc->first()->awal_tahun_kredit;
            $awal_pakai_debit = $nc->first()->awal_pakai_debit;
            $awal_pakai_kredit = $nc->first()->awal_pakai_kredit;
            $th_ini_debit = $nc->first()->awal_tahun_debit;
            $th_ini_kredit = $nc->first()->awal_tahun_kredit;
            $sd_bln_ini_debit = $sd_bln_lalu_debit;
            $sd_bln_ini_kredit = $sd_bln_lalu_kredit;
        }

        $fintrx =  $data;
        $result =  [
            'bl' => $bl,
            'trx' => $fintrx,
            'posisi' => $posisi,
            /* 'tgl_awal_pakai'=>$tgl_awal_pakai,*/
            'nama_buku' => $nama_buku,
            'kode_buku' => $kode_buku,
            'sd_bln_ini_debit' => $sd_bln_ini_debit,
            'sd_bln_ini_kredit' => $sd_bln_ini_kredit,
            'sd_bln_lalu_debit' => $sd_bln_lalu_debit,
            'sd_bln_lalu_kredit' => $sd_bln_lalu_kredit,
            'th_ini_debit' => $th_ini_debit,
            'th_ini_kredit' => $th_ini_kredit,
            'awal_tahun_debit' => $awal_tahun_debit,
            'awal_tahun_kredit' => $awal_tahun_kredit,
            'awal_pakai_debit' => $awal_pakai_debit,
            'awal_pakai_kredit' => $awal_pakai_kredit,
        ];

        if ($req->type == 'pdf') {
            // ini_set('max_execution_time', -1);
            $pdf = PDF::loadView('laporan.buku_besar.preview', $result);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download('buku_besar.pdf');
        } elseif ($req->type == 'excel') {
            die('Under construction');
        } else {
            //  ini_set('max_execution_time', -1);
            return view('laporan.buku_besar.' . $req->type, $result);
        }
    }

    public function arraySort($array)
    {
        $sort_id['id'] = [];
        foreach ($array as $key => $value) {

            $sort_key = $value['id'];
            $result = array_filter($sort_id['id'], function ($key) use ($sort_key) {
                return floor($key) == $sort_key;
            }, ARRAY_FILTER_USE_KEY);

            if (count($result) <= 0) {
                $sort_key = floatval($sort_key) + 0.01;
            } else {
                $jumlah_key = str_pad(count($result) + 1, 2, "0", STR_PAD_LEFT);
                $sort_key = floatval("$sort_key.$jumlah_key");
            }

            $sort_id['id']["$sort_key"] = $value;
        }

        ksort($sort_id['id']);

        $sort_tgl = [];
        foreach ($sort_id['id'] as $k => $v) {
            $tanggal = $v['tanggal'];
            $sort_tgl[strtotime($tanggal)][$k] = $v;
        }

        ksort($sort_tgl, SORT_NUMERIC);

        $data = [];

        foreach ($sort_tgl as $x => $row) {

            foreach ($row as $i => $j) {
                $data[] = $j;
            }
        }

        return $data;
    }

    public function cover(Request $req)
    {
        $business_id = auth()->user()->business_id;

        $bl = BusinessLocation::where('business_id', '=', $business_id)->first();

        if ($req->type == 'preview') {
            return view('laporan.cover.' . $req->type, compact('bl'));
        }
    }

    public function inventaris(Request $req)
    {
        $jenis_laporan = $req->segment(2);

        $tgl = ($req->tgl) ? $req->tgl : date('d');
        $bln = ($req->bln) ? $req->bln : date('m');
        $thn = ($req->thn) ? $req->thn : date('Y');

        $business_id = auth()->user()->business_id;
        if (isset($req->tgl)) {
            $tgl_seleksi = "{$thn}-{$bln}-{$tgl}";
        } else {
            $jumlah_hari = date('t', strtotime("{$thn}-{$bln}-01"));
            $tgl_seleksi = "{$thn}-{$bln}-{$jumlah_hari}";
        };

        $tgl_selected = "$thn-$bln-$tgl";

        $nama_awal = auth()->user()->first_name;
        $nama_akhir = auth()->user()->last_name;
        $ttd = $nama_awal . ' ' . $nama_akhir;
        $bl = BusinessLocation::where('business_id', '=', $business_id)->first();
        $jenisbuku = JenisBuku::where('business_id', auth()->user()->business_id)->get();
        $jurnal = DB::table('inventaris')->whereDate('tgl_beli', '<=', $tgl_seleksi)
            ->join('jurnal', 'id_jurnal', '=', 'jurnal.id')
            ->where('inventaris.business_id', $business_id);

        if ($jenis_laporan == 'inventaris') {
            $jurnal = $jurnal->where('jurnal.kd_rekening_debit', 'like', '151%');
        } elseif ($jenis_laporan == 'biaya_dibayar_dimuka') {
            $jurnal = $jurnal->where('jurnal.kd_rekening_debit', 'like', '141%');
        } elseif ($jenis_laporan == 'aktiva_tetap') {
            $jurnal = $jurnal->where('jurnal.kd_rekening_debit', 'like', '161%');
        }

        $jurnal = $jurnal->orderBy('tgl_beli', 'ASC')->get();
        $sekarang = Carbon::now()->timezone('Asia/Bangkok')->format('d-m-Y');
        $awal_tahun = Carbon::now()->startOfYear();
        $jmltotal = $jurnal->sum('unit');
        $jml_total = $jurnal->sum('nominal');
        $init = Carbon::now()->timezone('Asia/Bangkok')->startOfYear();
        $pengawas = User::where('business_id', $business_id)->where('legalitation', 'Pengawas')->first();
        $keuangan = User::where('business_id', $business_id)->where('legalitation', 'Bag. Keuangan')->first();
        $direktur = User::where('business_id', $business_id)->where('legalitation', 'Sebagai Direktur')->first();

        $array = [];
        $jml_biaya = 0;
        $jml_akum = 0;
        $jml_nb = 0;
        $jml_unit = 0;
        $jml_nilai = 0;
        $tot_unit = 0;
        $tot_harga_rusak = 0;

        foreach ($jurnal as $key => $value) {
            $umur = $this->umur($value, $tgl_selected);

            if ($value->status == 'baik') {
                $nilai = round($umur['nilai']);
            } elseif ($value->status == 'rusak') {
                $nilai = 1;
            } else {
                $nilai = 0;
            }

            $tgl_beli = explode('-', $value->tgl_beli);
            $allowed_status = ['baik', 'rusak'];

            if (in_array($value->status, $allowed_status) == false && $value->umur_ekonomis == $umur['pakai'] && $tgl_beli[0] < $thn) {
                $tot_unit += $value->unit;
                $tot_harga_rusak += $value->nominal;
            } else {
                if ($jenis_laporan == 'aktiva_tetap') {
                    $umur['biaya'] = 0;
                    $nilai = $value->unit * $value->harsat;
                }
                $array[] = [
                    'nama_barang' => $value->nama_barang,
                    'tgl_beli' => $value->tgl_beli,
                    'unit' => $value->unit,
                    'id' => $value->id,
                    'status' => $value->status,
                    'harsat' => $value->harsat,
                    'nominal' => $value->nominal,
                    'umur_ekonomis' => $value->umur_ekonomis,
                    'satuan' => number_format($value->nominal / $value->umur_ekonomis),
                    'umur_pakai' => $umur['tahun_ini'],
                    'biaya' => number_format($umur['biaya_tahun_ini']),
                    'umurpakai' => $umur['pakai'],
                    'akum' => number_format($umur['biaya']),
                    'nilai' => $nilai,
                    'tgl_validasi' => $value->tgl_validasi
                ];
            }

            $jml_biaya += round($umur['biaya_tahun_ini']);
            $jml_akum += round($umur['biaya']);
            $jml_nb += round($umur['nilai']);
            $jml_nilai += $nilai;
        }

        $result = [
            'bl' => $bl,
            'ttd' => $ttd,
            'biaya' => $jml_biaya,
            'jenisbuku' => $jenisbuku,
            'jurnal' => $array,
            'sekarang' => $sekarang,
            'jmltotal' => $jmltotal,
            'jml_total' => $jml_total,
            'awal_tahun' => $awal_tahun,
            'init'      => $init,
            'akum' => $jml_akum,
            'nilai' => $jml_nb,
            'tgl_seleksi' => $tgl_seleksi,
            'jml_nilai' => $jml_nilai,
            'hr' => $tot_harga_rusak,
            't_unit' => $tot_unit,
            'tgl' => $tgl,
            'bln' => $bln,
            'thn' => $thn,
            'pengawas' => $pengawas,
            'keuangan' => $keuangan,
            'direktur' => $direktur,
            'jenis_laporan' => ucwords(str_replace('_', ' ', $jenis_laporan))
        ];

        if ($req->type == 'pdf') {
            ini_set('max_execution_time', 7200);
            $pdf = PDF::loadView('laporan.inventaris.preview', $result);
            $pdf->setPaper('A4', 'landscape');

            return $pdf->download('inventaris-' . $tgl . '-' . $bln . '-' . $thn . '.pdf');
        } else {
            ini_set('max_execution_time', 7200);
            // return view('laporan.laba_rugi.'.$req->type,compact('bl','jb_pendapatan','jb_biaya'));
            return view('laporan.inventaris.' . $req->type, $result);
        }
        // return view('laporan.inventaris.'.$req->type,compact('bl','jenisbuku'));
    }

    public function umur($value, $date = null)
    {
        $awal = Carbon::now()->startOfYear();
        if ($date == null) $date = Carbon::now()->format('Y-m-d');
        $init = Carbon::now()->startOfYear();

        $biaya_susut = ceil($value->nominal / (int) $value->umur_ekonomis);

        $tahun_ini       = $this->bulan($init, $date, 'month', $value->tgl_beli, $awal);
        $biaya_tahun_ini = $biaya_susut * $tahun_ini;
        $pakai           = $this->bulan($value->tgl_beli, $date, 'month', $value->tgl_beli, $awal);;
        $biaya           = $biaya_susut * $pakai;
        $nilai           = $pakai ? $value->nominal - $biaya : $value->nominal;
        $biaya1          = $biaya_susut * $value->umur_ekonomis;

        if ($pakai >= $value->umur_ekonomis) {
            $data = [
                'tahun_ini'       => 0,
                'biaya_tahun_ini' => 0,
                'pakai'           => $pakai = $value->umur_ekonomis,
                'biaya'           => $biaya1 - 1,
                'nilai'           => 1,
            ];
        } else {
            $data = [
                'tahun_ini'       => $tahun_ini,
                'biaya_tahun_ini' => $biaya_tahun_ini,
                'pakai'           => $pakai,
                'biaya'           => $biaya,
                'nilai'           => $nilai,
            ];
        }
        return $data;
    }

    public function bulan($start, $end, $period = "month", $init, $awal_th)
    {
        $awal_th = Carbon::now()->startOfYear();

        $day = 0;
        $month = 1;
        // if ($awal_th == $start) {
        //     $month = 1;
        // }

        $month_array = array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $datestart = strtotime($start);
        $dateend = strtotime($end);
        $init_detik = strtotime($init);
        $month_start = intVal(strftime("%m", $datestart));
        $current_year = strftime("%y", $datestart);
        $diff = $dateend - $datestart;
        $date = $diff / (60 * 60 * 24);
        $day = $date;

        $awal = 1;

        while ($date > 0) {
            if ($awal) {
                $loop = $month_start - 1;
                $awal = 0;
            } else {
                $loop = 0;
            }

            for ($i = $loop; $i < 12; $i++) {
                if ($current_year % 4 == 0 && $i == 1)
                    $day_of_month = 29;
                else
                    $day_of_month = $month_array[$i];

                $date -= $day_of_month;

                if ($date <= 0) {
                    if ($date == 0)
                        $month++;
                    break;
                }
                $month++;
                // var_dump($month);
            }

            $current_year++;
        }

        // die;


        if (isset($init)) {
            $awal_tahun_beli = Carbon::parse($init)->startOfYear();
            if ($awal_tahun_beli == $awal_th) {
                $umur_kurang_setahun =  $dateend - $init_detik;
                $umur_bulan = $umur_kurang_setahun / (60 * 60 * 24 * 30);
                $umur_bulan = round($umur_bulan, 0, PHP_ROUND_HALF_DOWN);
                $month = $umur_bulan;
            }
        }
        switch ($period) {
            case "day":
                return $day;
                break;
            case "month":
                return $month;
                break;
            case "year":
                return intval($month / 12);
                break;
        }
    }
}
