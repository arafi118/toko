<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\JenisBuku;
use App\RekeningRiil;
use App\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterController extends Controller
{
    public function index()
    {
        $rek = [];
        $rekening_rill = RekeningRiil::all();
        foreach ($rekening_rill as $rr) {
            $rek[$rr['kd_rr']] = $rr['nama_rr'];
        }

        $jb = [];
        $jenis_buku = JenisBuku::all();
        foreach ($jenis_buku as $j) {
            $jb[$j['kd_jb']] = $j['kd_jb'] . '. ' . $j['nama_jb'];
        }

        $prov = [];
        $wilayah = Wilayah::whereRaw('LENGTH(kode)=2')->orderBy('nama', 'ASC')->get();
        foreach ($wilayah as $w) {
            $prov[$w['kode']] = $w['nama'];
        }

        return view('master.index')->with(compact('rek', 'jb', 'prov'));
    }

    public function kab($kd_prov)
    {
        $wilayah = Wilayah::whereRaw('LENGTH(kode)=5 AND kode LIKE "' . $kd_prov . '%"')->orderBy('nama', 'ASC')->get();
        return response()->json([
            'data' => $wilayah
        ]);
    }

    public function kec($kd_kab)
    {
        $wilayah = Wilayah::whereRaw('LENGTH(kode)=8 AND kode LIKE "' . $kd_kab . '%"')->orderBy('nama', 'ASC')->get();
        return response()->json([
            'data' => $wilayah
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->only([
            "nama_usaha",
            "provinsi",
            "kabupaten",
            "kode_pos"
        ]);

        $prov = Wilayah::where('kode', $data['provinsi'])->first();
        $kab = Wilayah::where('kode', $data['kabupaten'])->first();

        $nama_usaha = ucwords(strtolower($data['nama_usaha']));
        $nama_prov = ucwords(strtolower($prov->nama));
        $nama_kab = strtolower($kab->nama);
        $nama_kab = str_replace('kab. ', '', $nama_kab);
        $nama_kab = str_replace('kota ', '', $nama_kab);
        $nama_kab = ucwords($nama_kab);

        $master = 'siupk_master_toko';
        $database = env('DB_PREFIX') . '_' . str_replace(' ', '_', strtolower($data['nama_usaha']));
        $create_db = DB::statement("CREATE DATABASE IF NOT EXISTS {$database}");

        $info_database = DB::select("SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$master}'");
        foreach ($info_database as $info) {
            $table = $info->TABLE_NAME;

            DB::statement("CREATE TABLE IF NOT EXISTS {$database}.{$table} LIKE {$master}.{$table}");
            DB::statement("TRUNCATE TABLE {$database}.{$table}");
            DB::statement("INSERT INTO {$database}.{$table} SELECT * FROM {$master}.{$table}");
        }

        DB::update("UPDATE {$database}.business SET name = ?, start_date = ? WHERE 1", [$nama_usaha, date('Y-m-d')]);
        DB::update("UPDATE {$database}.business_locations SET name = ?, landmark = ?, country = 'Indonesia', state = ?, city = ?, zip_code = ?, email = ? WHERE 1", [
            $nama_usaha,
            $nama_usaha,
            $nama_prov,
            $nama_kab,
            $data['kode_pos'],
            str_replace(' ', '', strtolower($nama_usaha)) . '@gmail.com'
        ]);

        return redirect()->back()->with('success', "Toko {$nama_usaha} berhasil ditambahkan.");
    }

    public function jenisBuku(Request $request)
    {
        $data = $request->only([
            "posisi",
            "jenis_rekening",
            "kode",
            "nama",
            "inisial",
        ]);

        $info_database = DB::select("SELECT TABLE_SCHEMA FROM information_schema.TABLES WHERE TABLE_SCHEMA LIKE 'siupk_%' GROUP BY TABLE_SCHEMA");
        foreach ($info_database as $db) {
            $database = $db->TABLE_SCHEMA;
            $tb = DB::table('information_schema.TABLES')->select(DB::raw('COUNT(*) AS jumlah'))->where('TABLE_SCHEMA', '=', $database)->first();;
            $jumlah = $tb->jumlah;

            if ($jumlah >= 60 && $jumlah <= 61) {
                $jb = DB::table($database . '.jenis_buku')->first();
                $jumlah_jb = DB::table($database . '.jenis_buku')->where('kd_jb', $data['kode'])->count();

                if ($jumlah_jb < 1) {
                    DB::table($database . '.jenis_buku')->insert([
                        'business_id' => $jb->business_id,
                        'unit' => $jb->unit,
                        'posisi' => $data['posisi'],
                        'kd_jr' => $data['jenis_rekening'],
                        'kd_jb' => $data['kode'],
                        'ins' => $data['inisial'],
                        'nama_jb' => $data['nama'],
                        'icon' => '0',
                        'ap' => '0',
                        'file' => '0',
                        'lokasi' => '0',
                        'kd_kab' => '0',
                        'kecuali' => '0',
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', "Jenis Buku {$data['kode']} berhasil ditambahkan ke semua lokasi.");
    }

    public function rekening(Request $request)
    {
        $data = $request->only([
            "jenis_buku",
            "kode_rekening",
            "nama_rekening",
            "jenis_buku_pasangan",
            "kode_rekening_pasangan",
        ]);

        $kd_rekening = $data['jenis_buku'] . '.' . $data['kode_rekening'];
        $kd_rekening_pasangan = $data['jenis_buku_pasangan'] . '.' . $data['kode_rekening_pasangan'];

        $info_database = DB::select("SELECT TABLE_SCHEMA FROM information_schema.TABLES WHERE TABLE_SCHEMA LIKE 'siupk_%' GROUP BY TABLE_SCHEMA");
        foreach ($info_database as $db) {
            $database = $db->TABLE_SCHEMA;
            $tb = DB::table('information_schema.TABLES')->select(DB::raw('COUNT(*) AS jumlah'))->where('TABLE_SCHEMA', '=', $database)->first();;
            $jumlah = $tb->jumlah;

            if ($jumlah >= 60 && $jumlah <= 61) {
                $rek = DB::table($database . '.rekening')->first();
                $jumlah_rek = DB::table($database . '.rekening')->where('kd_rekening', $kd_rekening)->count();

                $posisi_rek = 2;
                $posisi_rek_pasangan = 1;
                if ($data['jenis_buku'] < 200) {
                    $posisi_rek = 1;
                    $posisi_rek_pasangan = 2;
                }

                // if ($jumlah_rek > 0) {
                //     DB::table($database . '.rekening')->where('kd_rekening', $kd_rekening)->delete();
                //     DB::table($database . '.rekening')->where('kd_rekening', $kd_rekening_pasangan)->delete();
                // }

                if ($jumlah_rek < 1) {
                    $rekening = DB::table($database . '.rekening')->insert([
                        "kd_jb" => $data['jenis_buku'],
                        "kd_rekening" => $kd_rekening,
                        "business_id" => $rek->business_id,
                        "nama_rekening" => $data['nama_rekening'],
                        "pasangan" => $kd_rekening_pasangan,
                        'tgl_awal' => $rek->tgl_awal,
                        "awal" => "0",
                        "posisi" => $posisi_rek,
                        "jenis_mutasi" => "debit",
                    ]);

                    $rekening = DB::table($database . '.rekening')->insert([
                        "kd_jb" => $data['jenis_buku_pasangan'],
                        "kd_rekening" => $kd_rekening_pasangan,
                        "business_id" => $rek->business_id,
                        "nama_rekening" => $data['nama_rekening'],
                        "pasangan" => $kd_rekening,
                        'tgl_awal' => $rek->tgl_awal,
                        "awal" => "0",
                        "posisi" => $posisi_rek_pasangan,
                        "jenis_mutasi" => "kredit",
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', "Rekening {$data['nama_rekening']} ({$kd_rekening}) berhasil ditambahkan ke semua lokasi.");
    }
}
