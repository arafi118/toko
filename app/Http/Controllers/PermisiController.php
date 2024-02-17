<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JenisBuku;
use App\Business;
use App\BusinessLocation;
use App\RekeningRiil;
use App\Rekening;
use App\RekeningOjk;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermisiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $string = "aku";
        // dd($string);
        Permission::where('name', 'sell.sell_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function neraca(Request $req){
        // $business_id = $req->business_id;
        // $jenis_buku = JenisBuku::where('business_id', $req->business_id)->get();
        
        $rek_ojk = RekeningOjk::all();
        
        foreach ($rek_ojk as $data) {
            $rek = $data->rekening;
            $dt_ku[] = [
                'rek' => $rek
            ];
            $reks = explode("#", $rek);
            echo "<pre>";
            print_r($reks);
            echo "</pre>";
            // echo $data->nama_akun;
            // foreach ($reks as $rek2) {
            //     $rek_2 = trim($rek);
                // echo $rek_2;
                // echo "<br>";
            // }
        }
        
        // echo "<pre>";
        // print_r($dt_ku);
        // echo "</pre>";
        $response = [
            'msg' => 'Data Rek OJK',
            'data' => $dt_ku
        ];
        return response()->json($response, 200);
    }

    public function view(Request $request)
    {
        // return 'It works';
        $tgl = $request->input('tgl');
        $bln = $request->input('bln');
        $thn = $request->input('thn');

        $data =[
            'tgl' => $tgl,
            'bln' => $bln,
            'thn' => $thn,
        ];
        // dd($request);
        $response = [
            'msg' => 'Data request',
            'data' => $data
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
