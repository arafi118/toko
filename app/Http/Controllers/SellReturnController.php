<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\BusinessLocation;
use App\Transaction;
use App\TaxRate;
use App\Variation;
use App\TransactionPayment;

use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\ContactUtil;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;

use Yajra\DataTables\Facades\DataTables;

class SellReturnController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $contactUtil;
    protected $businessUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ContactUtil $contactUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->contactUtil = $contactUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                    
                    ->join(
                        'business_locations AS bl',
                        'transactions.location_id',
                        '=',
                        'bl.id'
                    )
                    ->join(
                        'transactions as T1',
                        'transactions.return_parent_id',
                        '=',
                        'T1.id'
                    )
                    ->leftJoin(
                        'transaction_payments AS TP',
                        'transactions.id',
                        '=',
                        'TP.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell_return')
                    ->where('transactions.status', 'final')
                    ->select(
                        'transactions.id',
                        'transactions.transaction_date',
                        'transactions.invoice_no',
                        'contacts.name',
                        'transactions.final_total',
                        'transactions.payment_status',
                        'bl.name as business_location',
                        'T1.invoice_no as parent_sale',
                        'T1.id as parent_sale_id',
                        DB::raw('SUM(TP.amount) as amount_paid')
                    );

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }

            //Add condition for created_by,used in sales representative sales report
            if (request()->has('created_by')) {
                $created_by = request()->get('created_by');
                if (!empty($created_by)) {
                    $sells->where('transactions.created_by', $created_by);
                }
            }

            //Add condition for location,used in sales representative expense report
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $sells->where('transactions.location_id', $location_id);
                }
            }

            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                        ->whereDate('transactions.transaction_date', '<=', $end);
            }

            $sells->groupBy('transactions.id');

            return Datatables::of($sells)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    @if(auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access") )
                        <li><a href="#" class="btn-modal" data-container=".view_modal" data-href="{{action(\'SellReturnController@show\', [$parent_sale_id])}}"><i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")</a></li>
                        <li><a href="{{action(\'SellReturnController@add\', [$parent_sale_id])}}" ><i class="fa fa-edit" aria-hidden="true"></i> @lang("messages.edit")</a></li>
                    @endif

                    @if(auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access") )
                        <li><a href="#" class="print-invoice" data-href="{{action(\'SellReturnController@printInvoice\', [$id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a></li>
                    @endif
                    </ul>
                    </div>'
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('parent_sale', function($row){
                    return '<button type="button" class="btn btn-link btn-modal" data-container=".view_modal" data-href="' . action('SellController@show', [$row->parent_sale_id]) . '">' . $row->parent_sale . '</button>';
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn(
                    'payment_status',
                    '<a href="{{ action("TransactionPaymentController@show", [$id])}}" class="view_payment_modal payment-status payment-status-label" data-orig-value="{{$payment_status}}" data-status-name="{{__(\'lang_v1.\' . $payment_status)}}"><span class="label @payment_status($payment_status)">{{__(\'lang_v1.\' . $payment_status)}}</span></a>'
                )
                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;
                    return '<span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</sapn>';
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return  action('SellReturnController@show', [$row->parent_sale_id]) ;
                        } else {
                            return '';
                        }
                    }])
                ->rawColumns(['final_total', 'action', 'parent_sale', 'payment_status', 'payment_due'])
                ->make(true);
        }

        return view('sell_return.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     if (!auth()->user()->can('sell.create')) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     $business_id = request()->session()->get('user.business_id');

    //     //Check if subscribed or not
    //     if (!$this->moduleUtil->isSubscribed($business_id)) {
    //         return $this->moduleUtil->expiredResponse(action('SellReturnController@index'));
    //     }

    //     $business_locations = BusinessLocation::forDropdown($business_id);
    //     //$walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);

    //     return view('sell_return.create')
    //         ->with(compact('business_locations'));
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add($id)
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $sell = Transaction::where('business_id', $business_id)
                            ->with(['sell_lines', 'location', 'return_parent', 'contact', 'tax'])
                            ->find($id);

        foreach ($sell->sell_lines as $key => $value) {
            $sell->sell_lines[$key]->formatted_qty = $this->transactionUtil->num_f($value->quantity);
        }

        // $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->transactionUtil->payment_types();

        return view('sell_return.add')
            ->with(compact('sell','payment_types'));
    }
    public function addReturAsli($id)
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        }

        $sell = Transaction::where('business_id', $business_id)
                            ->with(['sell_lines', 'location', 'return_parent', 'contact', 'tax'])
                            ->find($id);

        foreach ($sell->sell_lines as $key => $value) {
            $sell->sell_lines[$key]->formatted_qty = $this->transactionUtil->num_f($value->quantity);
        }

        return view('sell_return.add')
            ->with(compact('sell'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('sell.create') || !auth()->user()->can('sell.payments')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->except('_token');
            // dd($input['jumlah']); 
            // dd($input); 
            

            if (!empty($input['products'])) {
                $business_id = $request->session()->get('user.business_id');
                
                //Check if subscribed or not
                if (!$this->moduleUtil->isSubscribed($business_id)) {
                    return $this->moduleUtil->expiredResponse(action('SellReturnController@index'));
                }
                
        
                $user_id = $request->session()->get('user.id');
                
                $discount = ['discount_type' => $input['discount_type'],
                                'discount_amount' => $input['discount_amount']
                            ];
                            
                $invoice_total = $this->productUtil->calculateInvoiceTotal($input['products'], $input['tax_id'], $discount);
                // dd($invoice_total);
                //Get parent sale
                $sell = Transaction::where('business_id', $business_id)
                ->with(['sell_lines'])
                ->findOrFail($input['transaction_id']);
                
                // dd($sell);
                //Check if any sell return exists for the sale
                $sell_return = Transaction::where('business_id', $business_id)
                        ->where('type', 'sell_return')
                        ->where('return_parent_id', $sell->id)
                        ->first();
                        
                $sell_return_data = [
                    // 'transaction_date' => $this->productUtil->uf_date($request->input('transaction_date')),
                    'transaction_date' => \Carbon::now()->toDateTimeString(),
                    'invoice_no' => $input['invoice_no'],
                    'discount_type' => $discount['discount_type'],
                    'discount_amount' => $this->productUtil->num_uf($input['discount_amount']),
                    'tax_id' => $input['tax_id'],
                    'tax_amount' => $invoice_total['tax'],
                    'total_before_tax' => $invoice_total['total_before_tax'],
                    'final_total' => $invoice_total['final_total']
                ];
 
                DB::beginTransaction();
                
                //Generate reference number
                if (empty($sell_return_data['invoice_no'])) {
                    //Update reference count
                    $ref_count = $this->productUtil->setAndGetReferenceCount('sell_return');
                    $sell_return_data['invoice_no'] = $this->productUtil->generateReferenceNumber('sell_return', $ref_count);
                }

                if(empty($sell_return)){
                    $sell_return_data['business_id'] = $business_id;
                    $sell_return_data['location_id'] = $sell->location_id;
                    $sell_return_data['contact_id'] = $sell->contact_id;
                    $sell_return_data['customer_group_id'] = $sell->customer_group_id;
                    $sell_return_data['type'] = 'sell_return';
                    $sell_return_data['status'] = 'final';
                    $sell_return_data['created_by'] = $user_id;
                    $sell_return_data['return_parent_id'] = $sell->id;
                    $sell_return = Transaction::create($sell_return_data);
                } else {
                    $sell_return->update($sell_return_data);
                }

                //Update quantity returned in sell line
                $returns = [];
                $product_lines = $request->input('products');
                foreach ($product_lines as $product_line) {
                    $returns[$product_line['sell_line_id']] = $product_line['quantity'];
                }

                $variation2 = [];
                foreach ($sell->sell_lines as $sell_var) {
                    $variation2[$sell_var->id] = $sell_var->variation_id;
                }

                $harga = [];
                foreach ($product_lines as $product_line) {
                    $harga[$product_line['sell_line_id']] = $product_line['unit_price_inc_tax'];
                }
                // dd($variation2);
                // dd($sell->payment_lines);
                $sum_laba = 0;
                $sum_harga_pokok = 0;

                foreach ($sell->sell_lines as $sell_line) {
                    
                    if(array_key_exists($sell_line->id, $returns)){
                        $variation = Variation::where('product_id',$sell_line->product_id)
                        ->find($variation2[$sell_line->id]);

                        $quantity = $this->transactionUtil->num_uf($returns[$sell_line->id]);
                        $quantity_before = $this->transactionUtil->num_f($sell_line->quantity_returned);
                        $sell_line->quantity_returned  = $quantity;

                        $hpp = $variation['default_purchase_price'];
                        $harga_input = $this->transactionUtil->num_uf($harga[$sell_line->id]);
                        $laba = $harga_input - $hpp;

                        // dd($this->transactionUtil->num_uf($returns[$sell_line->id]));
                        $sum_harga_pokok += $hpp * $this->transactionUtil->num_uf($returns[$sell_line->id]);
                        $sum_laba += $laba * $this->transactionUtil->num_uf($returns[$sell_line->id]);
                        // dd($sum_harga_pokok);

                        if((int) $returns[$sell_line->id] > 0){
                            // $sell_line->id_rekening_debit  = '131.04';
                            // $sell_line->id_rekening_kredit = '111.12';
                            $sell_line->created_at = date('Y-m-d H:i:s');
                        }
                        $sell_line->save();

                        //update quantity sold in corresponding purchase lines
                        $this->transactionUtil->updateQuantitySoldFromSellLine($sell_line, $returns[$sell_line->id], $quantity_before);

                        // Update quantity in variation location details
                        $this->productUtil->updateProductQuantity($sell_return->location_id, $sell_line->product_id, $sell_line->variation_id, $returns[$sell_line->id], $quantity_before);
                    }
                }
                $jumlah = $this->transactionUtil->num_uf($input['jumlah']);
                $sisa = $invoice_total['final_total'] - $jumlah;
                
                $tipe_bayar = '';
                if($sisa > 0){
                    if($sisa == $invoice_total['final_total']){
                        $tipe_bayar = 'hutang';
                    }else{
                        $tipe_bayar = 'pembayaran_sebagian';
                    }
            
                }elseif($sisa == 0){
                    $tipe_bayar = 'lunas';
                }
                
                // $jum = [];
                
                // $jum['jum_input'] = $jumlah;
                // $jum['pokok'] = $sum_harga_pokok;
                // $jum['laba'] = $sum_laba;
                // $jum['total'] = $sum_harga_pokok + $sum_laba;
                $persen = $jumlah / $invoice_total['final_total'];
                $laba_bayar = $sum_laba * round($persen, 2);
                $pokok_bayar = $sum_harga_pokok * round($persen, 2);
                
                $method = $input['method'];
                // dd($method);
                $rek_hpp = $this->transactionUtil->accountNumbering('sell_return',$method,$tipe_bayar,'harga_pokok_penjualan');
                $rek_laba = $this->transactionUtil->accountNumbering('sell_return',$method,$tipe_bayar,'laba');
                // dd($request->input('transaction_date'));

                $bayar_hpp = [];
                $bayar_hpp['amount']                = $pokok_bayar;
                // $bayar_hpp['paid_on']               = \Carbon::createFromFormat('m/d/Y', $request->input('transaction_date'))->toDateTimeString();
                $bayar_hpp['paid_on']               = \Carbon::now()->toDateTimeString();
                //  dd($bayar_hpp);

                $bayar_hpp['created_by']            = auth()->user()->id;
                $bayar_hpp['transaction_id']        = $sell_return->id;
                $bayar_hpp['payment_for']           = $sell->contact_id;
                $bayar_hpp['method']                = $method;
                $bayar_hpp['card_type']             = 'credit';

                $hitung = $this->transactionUtil->setAndGetReferenceCount('sell_return');
                //Generate reference number
                $bayar_hpp['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber('sell_return', $hitung);
                $bayar_hpp['business_id'] = $request->session()->get('business.id');
                $bayar_hpp['id_rekening_debit']  = $rek_hpp['kd_rekening_debit'];
                $bayar_hpp['id_rekening_kredit'] = $rek_hpp['kd_rekening_kredit'];
                
                $bayar_laba = [];
                $bayar_laba['amount']                = $laba_bayar;
                // $bayar_laba['paid_on']               = \Carbon::createFromFormat('m/d/Y', $request->input('transaction_date'))->toDateTimeString();
                $bayar_laba['paid_on']               = \Carbon::now()->toDateTimeString();
                $bayar_laba['created_by']            = auth()->user()->id;
                $bayar_laba['transaction_id']        = $sell_return->id;
                $bayar_laba['payment_for']           = $sell->contact_id;
                $bayar_laba['method']                = $method;
                $bayar_laba['card_type']             = 'credit';

                $hitung = $this->transactionUtil->setAndGetReferenceCount('sell_return');
                //Generate reference number
                $bayar_laba['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber('sell_return', $hitung);
                $bayar_laba['business_id'] = $request->session()->get('business.id');
                $bayar_laba['id_rekening_debit']  = $rek_laba['kd_rekening_debit'];
                $bayar_laba['id_rekening_kredit'] = $rek_laba['kd_rekening_kredit'];
// dd($bayar_laba);

                TransactionPayment::create($bayar_hpp);
                TransactionPayment::create($bayar_laba);
                // dd($bayar_hpp);


                //Update payment status
                $this->transactionUtil->updatePaymentStatus($sell_return->id, $sell_return->final_total);

                $receipt = $this->receiptContent($business_id, $sell_return->location_id, $sell_return->id);
               

                $output = ['success' => 1,
                            'msg' => __('lang_v1.success'),
                            // 'receipt' => $receipt
                        ];
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            } else {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $msg = __('messages.something_went_wrong');
            }

            $output = ['success' => 0,
                            'msg' => $msg
                        ];
        }
        

        // return $output;
        return redirect('sell-return')->with('status', $output);
        // return redirect('purchase-return')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('sell.view')) {
            abort(403, 'Unauthorized action.');
        }

        $id_retur = Transaction::select('id', 'payment_status')->where('return_parent_id',$id)->first();
        $business_id = request()->session()->get('user.business_id');
        $stt_bayar = Transaction::select('payment_status')->where('id',$id_retur->id)->first()->payment_status;
        // dd($business_id);
        
        $sell = Transaction::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with(
                                    'contact',
                                    'return_parent',
                                    'tax',
                                    'sell_lines',
                                    'sell_lines.product',
                                    'sell_lines.variations',
                                    'location'
                                )
                                ->with(array('payment_lines'=>function($query)use($business_id){
                                    // $business_id = request()->session()->get('user.business_id');
                                    $query->leftJoin('rekening','transaction_payments.id_rekening_debit','=','rekening.kd_rekening')
                                    // ->where('rekening.business_id', $business_id)
                                    // $query->where('rekening.business_id', $business_id);
                                    ->groupBy('transaction_payments.id');
                                }))
                                ->first();
        // dd($sell);
        $sell_taxes = [];
        if(!empty($sell->return_parent->tax)){
            if ($sell->return_parent->tax->is_tax_group) {
                $sell_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->return_parent->tax, $sell->return_parent->tax_amount));
            } else {
                $sell_taxes[$sell->return_parent->tax->name] = $sell->return_parent->tax_amount;
            }
        }

        $total_discount = 0;
        if($sell->return_parent->discount_type == 'fixed'){
            $total_discount = $sell->return_parent->discount_amount;
        } elseif ($sell->return_parent->discount_type == 'percentage') {
            $total_after_discount = $sell->return_parent->final_total - $sell->return_parent->tax_amount;
            $total_before_discount = $total_after_discount * 100 / (100 - $sell->return_parent->discount_amount);
            $total_discount = $total_before_discount - $total_after_discount;
        }
        
        return view('sell_return.show')
            ->with(compact('sell', 'sell_taxes', 'total_discount'));
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

    /**
     * Return the row for the product
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProductRow()
    {
    }

    /**
     * Returns the content for the receipt
     *
     * @param  int  $business_id
     * @param  int  $location_id
     * @param  int  $transaction_id
     * @param string $printer_type = null
     *
     * @return array
     */
    private function receiptContent(
        $business_id,
        $location_id,
        $transaction_id,
        $printer_type = null
    ) {
    
        $output = ['is_enabled' => false,
                    'print_type' => 'browser',
                    'html_content' => null,
                    'printer_config' => [],
                    'data' => []
                ];

        $business_details = $this->businessUtil->getDetails($business_id);
        $location_details = BusinessLocation::find($location_id);

        //Check if printing of invoice is enabled or not.
        if ($location_details->print_receipt_on_invoice == 1) {
            //If enabled, get print type.
            $output['is_enabled'] = true;

            $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $location_id, $location_details->invoice_layout_id);

            //Check if printer setting is provided.
            $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;

            $receipt_details = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type);
            
            //If print type browser - return the content, printer - return printer config data, and invoice format config
            if ($receipt_printer_type == 'printer') {
                $output['print_type'] = 'printer';
                $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
                $output['data'] = $receipt_details;
            } else {
                $output['html_content'] = view('sell_return.receipt', compact('receipt_details'))->render();
            }
        }

        return $output;
    }

    /**
     * Prints invoice for sell
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice(Request $request, $transaction_id)
    {
        if (request()->ajax()) {
            try {
                $output = ['success' => 0,
                        'msg' => trans("messages.something_went_wrong")
                        ];

                $business_id = $request->session()->get('user.business_id');
            
                $transaction = Transaction::where('business_id', $business_id)
                                ->where('id', $transaction_id)
                                ->first();

                if (empty($transaction)) {
                    return $output;
                }

                $receipt = $this->receiptContent($business_id, $transaction->location_id, $transaction_id, 'browser');

                if (!empty($receipt)) {
                    $output = ['success' => 1, 'receipt' => $receipt];
                }
            } catch (\Exception $e) {
                $output = ['success' => 0,
                        'msg' => trans("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }
}
