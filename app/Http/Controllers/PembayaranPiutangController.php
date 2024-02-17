<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\TransactionPayment;
use App\PembayaranPiutang;

class PembayaranPiutangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       if (!auth()->user()->can('pembayaran_piutang.create')) {
            abort(403, 'Unauthorized action.');
       }

        if (request()->ajax()) {
            $columns = ['transactions.id','transactions.ref_no','transaction_payments.payment_ref_no','transactions.transaction_date','transactions.final_total'];
            $piutang  = TransactionPayment::select($columns)
                                        ->leftJoin('transactions','transaction_payments.transaction_id','=','transactions.id')
                                        ->where('method','tempo')
                                        ->where('type','sell')
                                        ->where('status','final')
                                        ->where('transaction_payments.business_id',auth()->user()->business_id)
                                        ->groupBy('transactions.id')
                                        ;
          //dd($piutang->get());
            return Datatables::of($piutang)
                ->addColumn(
                    'action',
                    '@can("pembayaran_piutang.update")
                    <a href="{{action(\'PembayaranPiutangController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_pembayaran_piutang_button"><i class="glyphicon glyphicon-edit"></i>Bayar</a>
                        &nbsp;
                    @endcan
                    @can("pembayaran_piutang.delete")
                        <button data-href="{{action(\'PembayaranPiutangController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_pembayaran_piutang_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                 ->editColumn('amount',function($row){
                    return "Rp ".number_format($row->amount);
                })
                ->removeColumn('id')
                ->rawColumns([4])
                ->make(false);
        }


       return view('pembayaran_piutang.index');
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
        $business_id = auth()->user()->business_id;

        try {
            $ph             = new PembayaranPiutang;
            $ph->id_payment = $request->id_payment;
            $ph->business_id= $business_id;
            $ph->cara_bayar = $request->cara_bayar;
            $ph->no_rekening= $request->no_rekening;
            $ph->atas_nama_rekening = $request->atas_nama_rekening; 
            $ph->tgl_bayar  = \Carbon::createFromFormat('m/d/Y',$request->tgl_bayar)->format('Y-m-d');
            $ph->kd_invoice = $request->payment_ref_no;
            if($request->cara_bayar == 'kas'){
                $ph->kd_buku    = '132';
                $ph->kd_rekening_debit  = '111.08';
                $ph->kd_rekening_kredit = '132.03';            
            }else{
                $ph->kd_buku    = '132';
                $ph->kd_rekening_debit= '121.04';
                $ph->kd_rekening_kredit = '132.04';
            
            }
            $ph->nominal    = $request->jumlah_bayar;
            $ph->save();
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        }

        return redirect()->to('pembayaran_piutang/'.$request->id.'/edit')->with('success','Data Pembayaran Piutang Tersimpan');
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
        $columns = ['transactions.id','transactions.ref_no','transaction_payments.payment_ref_no','transactions.transaction_date','transactions.final_total','transaction_payments.id as payment_id'];
        $tp = TransactionPayment::select($columns)
                                ->leftJoin('transactions','transaction_payments.transaction_id','=','transactions.id')
                                ->where('transactions.id',$id)
                                ->where('transaction_payments.business_id',auth()->user()->business_id)
                                ->first();
        
        $piutangs = PembayaranPiutang::where('id_payment',$tp->payment_id)->get();
        $terbayar = PembayaranPiutang::selectRaw('SUM(nominal) as terbayar')->where('id_payment',$tp->payment_id)->first();
        
        return view('pembayaran_piutang.form',['tp'=>$tp,'piutangs'=>$piutangs,'terbayar'=>$terbayar]);
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

    public function hapusPembayaranPiutang($id,$payment_id)
    {
        $ph = PembayaranPiutang::find($id);
        $ph->delete();

        return redirect()->to('pembayaran_piutang/'.$payment_id.'/edit')->with('success','Data Pembayaran Piutang Terhapus');
    }
}
