<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\JenisBuku;
use App\Rekening;
use App\Jurnal;
use App\TransactionPayment;
use App\Inventaris;
use Carbon\Carbon;

class JurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('jurnal.view') && !auth()->user()->can('jurnal.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $columns = ['jurnal.id','jenis_buku.nama_jb','jenis_buku.kd_jb', 'kd_rekening_debit','kd_rekening_kredit','tanggal_jurnal','keterangan','nominal'];
            $business_id = request()->session()->get('user.business_id');
            $jurnal = Jurnal::select($columns)
                            ->leftJoin('jenis_buku','jurnal.kd_jenis_buku','=','jenis_buku.kd_jb')
                           // ->leftJoin('rekening','jurnal.kd_rekening','=','rekening.kd_rekening')
                            ->where('jurnal.business_id', $business_id)
                            ->groupBy('jurnal.id');
            return Datatables::of($jurnal)
                ->addColumn(
                    'action',
                    '@can("jurnal.update")
                    
                    <button data-href="{{action(\'JurnalController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_jurnal_button" @if($kd_jb == "151" || $kd_jb == "161" || $kd_rekening_debit == "151.01" || $kd_rekening_debit == "161.01") disabled @endif><i class="glyphicon glyphicon-edit" ></i>  @lang("messages.edit")</button>
                    &nbsp;
                    
                    @endcan
                    
                    @can("jurnal.delete")
                        <button data-href="{{action(\'JurnalController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_jurnal_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                
                
                ->editColumn('jenis_mutasi',function($row){
                    return ucfirst($row->jenis_mutasi);
                })
                ->editColumn('tanggal_jurnal',function($row){
                    return $row->tanggal_jurnal->format('d F Y');
                })
                 ->editColumn('nominal',function($row){
                    return "Rp ".number_format($row->nominal);
                })
                ->removeColumn('id','kd_jb')
                ->rawColumns([6])
                ->make(false);
        }
        return view('jurnal.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenisbuku = JenisBuku::whereNotIn('posisi',['4','5'])
        ->where('business_id',auth()->user()->business_id)
        ->orderBy('kd_jb')
        ->get();
        $rekening  = Rekening::where('business_id',auth()->user()->business_id)->orderBy('kd_rekening');
        $hutang    = TransactionPayment::selectRaw('transaction_payments.id as id,transaction_payments.amount,transaction_payments.payment_ref_no')
        ->leftJoin('transactions','transaction_payments.transaction_id','=','transactions.id')
        ->where('method','tempo')
        ->where('type','purchase')
        ->where('status','received')
        ->where('transaction_payments.business_id',auth()->user()->business_id)->get();

        $s = Inventaris::where('status', 'baik')->get();

        return view('jurnal.form', ['jenisbuku' => $jenisbuku, 'rekening' => $rekening, 'hutang' => $hutang, 'inventaris' => $s]);
       
        // return view('jurnal.form',['jenisbuku'=>$jenisbuku,'rekening'=>$rekening,'hutang'=>$hutang]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         
            $tanggaljurnal         = \Carbon::parse($request->tanggaljurnal)->format('Y-m-d');
            $business_id           = request()->session()->get('user.business_id');
            $jurnal                = new Jurnal;
            $jurnal->business_id   = $business_id;
            $jurnal->kd_jenis_buku = $request->jenisbuku;
            $jurnal->jenis_mutasi  = $request->jenismutasi;
            if($request->jenismutasi == 'debit'){
                $jurnal->kd_rekening_debit  = $request->namarekening;
                $jurnal->kd_rekening_kredit   = $request->namarekeningpasangan;
            }else{
                $jurnal->kd_rekening_kredit  = $request->namarekening;
                $jurnal->kd_rekening_debit   = $request->namarekeningpasangan;
            }
            
            $jurnal->tanggal_jurnal= $tanggaljurnal;
            $jurnal->keterangan    = $request->keterangan;
            $jurnal->nominal       = $request->nominal;
            $jurnal->ref_id        = $request->refhutang;
            $jurnal->created_by    = auth()->user()->id;
            
            $jurnal->save();
            
            if ($request->namarekening == '141.01' || $request->namarekening == '151.01' || $request->namarekening == '161.01' || $request->namarekening == '111.21') {
                $inven = [
                    "business_id"   => $business_id,
                    "id_jurnal"     => $jurnal->id,
                    "nama_barang"   => $request->keterangan,
                    "tgl_beli"      => $tanggaljurnal,
                    "unit"          => $request->jumlah,
                    "harsat"        => $request->nominal,
                    "umur_ekonomis" => $request->umur,
                    "jenis"         => '1'
                ];
                
                DB::table('inventaris')->insert($inven);
                $jurnal->nominal = $request->perolehan;
                $jurnal->save();
            }
            
            

            $output = ['success'   => true,
                            'data' => $jurnal,
                            'msg'  => 'Data Jurnal tersimpan'
                        ];
        try {
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                      'msg'     => __("messages.something_went_wrong")];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $edt       = Jurnal::find($id);
        $tbl_inventaris = DB::table('inventaris')->join('jurnal', 'id_jurnal', '=', 'jurnal.id')->where('id_jurnal', $edt->id)->get();
        $jenisbuku = JenisBuku::all();
        $s = Inventaris::where($id);
        $rekening  = Rekening::all();
        return view('jurnal.detail', [
            'edt' => $edt,
            's' => $s,
            'tbl_inventaris' => $tbl_inventaris
        ]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function penghapusan($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edt       = Jurnal::find($id);
        $jenisbuku = JenisBuku::whereNotIn('posisi',['4','5'])
        ->where('business_id',auth()->user()->business_id)
        ->orderBy('kd_jb')
        ->get();
        $rekening  = Rekening::all();
        $hutang    = TransactionPayment::selectRaw('transaction_payments.id as id,transaction_payments.amount,transaction_payments.payment_ref_no')
        ->leftJoin('transactions','transaction_payments.transaction_id','=','transactions.id')
        ->where('method','tempo')
        ->where('type','purchase')
        ->where('status','received')
        ->where('transaction_payments.business_id',auth()->user()->business_id)->get();
       
        return view('jurnal.edt_form',['jenisbuku'=>$jenisbuku,'rekening'=>$rekening,'edt'=>$edt,'hutang'=>$hutang]);
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
        try
        {

            $tanggaljurnal         = \Carbon::parse($request->tanggaljurnal)->format('Y-m-d');
            $business_id           = request()->session()->get('user.business_id');
            $jurnal                = Jurnal::find($id);
            $jurnal->business_id   = $business_id;
            // $jurnal->kd_jenis_buku = $request->jenisbuku;
            // $jurnal->jenis_mutasi  = $request->jenismutasi;
            // if($request->jenismutasi == 'debit'){
            //     $jurnal->kd_rekening_debit  = $request->namarekening;
            //     $jurnal->kd_rekening_kredit   = $request->namarekeningpasangan;
            // }else{
            //     $jurnal->kd_rekening_kredit  = $request->namarekening;
            //     $jurnal->kd_rekening_debit   = $request->namarekeningpasangan;
            // }
            $jurnal->tanggal_jurnal = $tanggaljurnal;
            $jurnal->keterangan    = $request->keterangan;
            $jurnal->nominal       = $request->nominal;
            // $jurnal->ref_id        = $request->refhutang;
            $jurnal->created_by    = auth()->user()->id;
            $jurnal->save();

            $output = [
                'success'   => true,
                'data' => $jurnal,
                'msg'  => 'Data Jurnal Berhasil Di Edit'
            ];
        }
        catch (\Exception $e)
        {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg'     => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('jurnal.delete'))
        {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax())
        {
            try
            {
                $business_id = request()->session()->get('user.business_id');

                $jurnal = Jurnal::where('business_id', $business_id)->findOrFail($id);
                $jurnal->delete();

                $output = [
                    'success' => true,
                    'msg' => "Data transaksi jurnal sudah terhapus"
                ];
            }
            catch (\Exception $e)
            {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    public function getRekening($id_jenis_buku,$jenis_trx)
    {
        $rekening = Rekening::where('kd_jb','=',$id_jenis_buku)
        ->where('business_id',auth()->user()->business_id)
        ->where('jenis_mutasi','=',$jenis_trx)
        ->orderBy('kd_rekening')
        // ->groupBy('kd_rekening')
        ->get();

        return response()->json(['success' => true, 'rekening' => $rekening]);
    }

    public function getPasangan($id_rekening)
    {
        $pasangan = Rekening::where('kd_rekening','=',$id_rekening)
        ->where('business_id',auth()->user()->business_id)
        ->first();

        return response()->json(['success'=>true,'pasangan'=>$pasangan]);
    }

    public function getHutang($id)
    {
        $tp = TransactionPayment::find($id);

        return response()->json(['success'=>true,'tp'=>$tp->amount]);
    }
}
