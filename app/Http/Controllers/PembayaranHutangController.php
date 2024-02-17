<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\TransactionPayment;
use App\PembayaranHutang;

class PembayaranHutangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       if (!auth()->user()->can('pembayaran_hutang.create')) {
            abort(403, 'Unauthorized action.');
       }

        if (request()->ajax()) {
            $columns = ['transaction_payments.id','transactions.ref_no','transaction_payments.payment_ref_no','transactions.transaction_date','transaction_payments.amount'];
            $hutang  = TransactionPayment::select($columns)
                                        ->leftJoin('transactions','transaction_payments.transaction_id','=','transactions.id')
                                        ->where('method','tempo')
                                        ->where('type','purchase')
                                        ->where('status','received')
                                        ->where('transaction_payments.business_id',auth()->user()->business_id);

            return Datatables::of($hutang)
                ->addColumn(
                    'action',
                    '@can("pembayaran_hutang.update")
                    <a href="{{action(\'PembayaranHutangController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_pembayaran_hutang_button"><i class="glyphicon glyphicon-edit"></i>Bayar</a>
                        &nbsp;
                    @endcan
                    @can("pembayaran_hutang.delete")
                        <button data-href="{{action(\'PembayaranHutangController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_pembayaran_hutang_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                 ->editColumn('amount',function($row){
                    return "Rp ".number_format($row->amount);
                })
                ->removeColumn('id')
                ->rawColumns([4])
                ->make(false);
        }


       return view('pembayaran_hutang.index');
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
            $ph             = new PembayaranHutang;
            $ph->id_payment = $request->id_payment;
            $ph->business_id= $business_id;
            $ph->cara_bayar = $request->cara_bayar;
            $ph->no_rekening= $request->no_rekening;
            $ph->atas_nama_rekening = $request->atas_nama_rekening; 
            $ph->tgl_bayar  = \Carbon::createFromFormat('m/d/Y',$request->tgl_bayar)->format('Y-m-d');
            $ph->kd_invoice = $request->payment_ref_no;
            if($request->cara_bayar == 'kas'){
                $ph->kd_buku    = '211';
                $ph->kd_rekening_debit  = '211.02';
                $ph->kd_rekening_kredit = '111.23';
            
            }else{
                $ph->kd_buku    = '211';
                $ph->kd_rekening_debit= '211.03';
                $ph->kd_rekening_kredit ='121.08';
            
            }
            $ph->nominal    = $request->jumlah_bayar;
            $ph->save();
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        }

        return redirect()->to('pembayaran_hutang/'.$request->id_payment.'/edit')->with('success','Data Pembayaran Hutang Tersimpan');
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
        $columns = ['transaction_payments.id','transactions.ref_no','transaction_payments.payment_ref_no','transactions.transaction_date','transaction_payments.amount'];
        $tp = TransactionPayment::select($columns)
                                ->leftJoin('transactions','transaction_payments.transaction_id','=','transactions.id')
                                ->where('transaction_payments.id',$id)
                                ->where('transaction_payments.business_id',auth()->user()->business_id)
                                ->first();

        $utangs = PembayaranHutang::where('id_payment',$tp->id)->get();
        $terbayar = PembayaranHutang::selectRaw('SUM(nominal) as terbayar')->where('id_payment',$tp->id)->first();
        
        return view('pembayaran_hutang.form',['tp'=>$tp,'utangs'=>$utangs,'terbayar'=>$terbayar]);
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

    public function hapusPembayaranHutang($id,$payment_id)
    {
        $ph = PembayaranHutang::find($id);
        $ph->delete();

        return redirect()->to('pembayaran_hutang/'.$payment_id.'/edit')->with('success','Data Pembayaran Hutang Terhapus');
    }
}
