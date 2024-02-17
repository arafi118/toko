<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\TaxRate;
use App\Transaction;
use App\TransactionPayment;
use App\BusinessLocation;
use App\TransactionSellLine;
use App\User;
use App\CustomerGroup;
use App\SellingPriceGroup;
use App\Variation;
use Yajra\DataTables\Facades\DataTables;
use DB;

use App\Utils\ProductUtil;
use App\Utils\ContactUtil;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
use App\Utils\ModuleUtil;

class PreOrderController extends Controller
{
    //
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $contactUtil;
    protected $businessUtil;
    protected $transactionUtil;


    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(
        ContactUtil $contactUtil,
        ProductUtil $productUtil,
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil
    )
    {
        $this->contactUtil = $contactUtil;
        $this->productUtil = $productUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;

        $this->dummyPaymentLine = ['method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '', 
        'is_return' => 0, 'transaction_no' => ''];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        if (!auth()->user()->can('sell.sell_list')) {
            abort(404);
        }

        // echo 'status';
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftJoin(
                    'transactions AS SR',
                    'transactions.id',
                    '=',
                    'SR.return_parent_id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'ordered')
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.is_direct_sale',
                    'transactions.invoice_no',
                    'contacts.name',
                    'transactions.payment_status',
                    'transactions.final_total',
                    DB::raw('SUM(IF(tp.is_return = 1,-1*tp.amount,tp.amount)) as total_paid'),
                    'bl.name as business_locations',
                    DB::raw('COUNT(SR.id) as return_exists'),
                    DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE
                        TP2.transaction_id=SR.id ) as return_paid'),
                    DB::raw('COALESCE(SR.final_total, 0) as amount_return'),
                    'SR.id as return_transaction_id'
                );

                // dd($sells->id);

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
                $end   = request()->end_date;
                $sells->whereDate('transactions.transaction_date', '>=', $start)
                            ->whereDate('transactions.transaction_date', '<=', $end);
            }

            //Check is_direct sell
            if (request()->has('is_direct_sale')) {
                $is_direct_sale = request()->is_direct_sale;
                if ($is_direct_sale == 0) {
                    $sells->where('transactions.is_direct_sale', 0);
                }
            }

            //Add condition for commission_agent,used in sales representative sales with commission report
            if (request()->has('commission_agent')) {
                $commission_agent = request()->get('commission_agent');
                if (!empty($commission_agent)) {
                    $sells->where('transactions.commission_agent', $commission_agent);
                }
            }

            if($this->moduleUtil->isModuleDefined('woocommerce')){
                $sells->addSelect('transactions.woocommerce_order_id');
            }
            $sells->groupBy('transactions.id');
            // dd($sells);
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
                        <li><a href="#" data-href="{{action(\'SellController@show\', [$id])}}" class="btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")</a></li>
                    @endif
                    
                    @can("sell.delete")
                    <li><a href="{{action(\'SellPosController@destroy\', [$id])}}" class="delete-sale"><i class="fa fa-trash"></i> @lang("messages.delete")</a></li>

                    <li><a href="{{action(\'PreOrderController@cancelPreOrder\', [$id])}}" class="cancel-pre-order"><i class="fa fa-undo"></i> Batal Pre-Order</a></li>
                    @endcan

                    @if(auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access") )
                        <li><a href="#" class="print-invoice" data-href="{{route(\'preorder.printPreOrder\', [$id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a></li>
                    @endif
                    
                    <li class="divider"></li> 
                    @if($payment_status != "paid")
                        @if(auth()->user()->can("sell.create") || auth()->user()->can("direct_sell.access") )
                            <li><a href="{{action(\'TransactionPaymentController@addPayOrder\', [$id])}}" class="add_payment_modal"><i class="fa fa-money"></i> @lang("purchase.add_payment")</a></li>
                        @endif
                    @endif
                        <li><a href="{{action(\'TransactionPaymentController@showPayOrder\', [$id])}}" class="view_payment_modal"><i class="fa fa-money"></i> @lang("purchase.view_payments")</a></li>
                    
                    
                    </ul></div>
                    '
                )
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->addColumn('total_paid', function ($row) {
                     $total_remaining = $this->checkPayment($row->id);

                     return $row->final_total - $total_remaining;
                })
             
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn(
                    'payment_status',
                    '<a href="{{ action("TransactionPaymentController@showPayOrder", [$id])}}" class="view_payment_modal payment-status-label" data-orig-value="{{$payment_status}}" data-status-name="{{__(\'lang_v1.\' . $payment_status)}}"><span class="label @payment_status($payment_status)">{{__(\'lang_v1.\' . $payment_status)}}
                        </span></a>'
                )
                ->addColumn('total_remaining', function ($row) {
                    $total_remaining = $this->checkPayment($row->id);
                   // $total_remaining =  $row->final_total - $row->total_paid;
                    $total_remaining_html = '<strong>Jml Pelunasan :</strong> <span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $total_remaining . '">' . $total_remaining . '</span>';

                    if(!empty($row->return_exists)){
                        $return_due = $row->amount_return - $row->return_paid;
                        $total_remaining_html .= '<br><strong>' . __('lang_v1.sell_return_due') .':</strong> <a href="' . action("TransactionPaymentController@show", [$row->return_transaction_id]) . '" class="view_purchase_return_payment_modal"><span class="display_currency" data-currency_symbol="true" data-orig-value="' . $return_due . '">' . $return_due . '</span></a>';
                    }
                    return $total_remaining_html;
                })
                 ->editColumn('invoice_no', function($row){
                    $invoice_no = $row->invoice_no;
                    if (!empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fa fa-wordpress text-primary" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }
                    if (!empty($row->return_exists)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round" title="' . __('lang_v1.some_qty_returned_from_sell') .'"><i class="fa fa-undo"></i></small>';
                    }

                    return $invoice_no;
                 })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return  action('PreOrderController@show', [$row->id]) ;
                        } else {
                            return '';
                        }
                    }])
                ->rawColumns(['final_total', 'action', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no'])
                ->make(true);
        }
        return view('preorder.index');

        // return view();
    }



    public function create()
    {
        if (!auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not, then check for users quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        } elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellController@index'));
        }

        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);
        
        $business_details = $this->businessUtil->getDetails($business_id);
        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];

        $default_location = null;
        if (count($business_locations) == 1) {
            foreach ($business_locations as $id => $name) {
                $default_location = $id;
            }
        }

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $commission_agent = [];
        if ($commsn_agnt_setting == 'user') {
            $commission_agent = User::forDropdown($business_id);
        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $commission_agent = User::saleCommissionAgentsDropdown($business_id);
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->transactionUtil->payment_types();

        //Selling Price Group Dropdown
        $price_groups = SellingPriceGroup::forDropdown($business_id);

        $default_datetime = $this->businessUtil->format_date('now', true);

        return view('preorder.create')
        ->with(compact(
            'business_details',
            'taxes',
            'walk_in_customer',
            'business_locations',
            'bl_attributes',
            'default_location',
            'commission_agent',
            'types',
            'customer_groups',
            'payment_line',
            'payment_types',
            'price_groups',
            'default_datetime'
        ));
    }


    public function store(Request $request)
    {
        // dd($request->all());
        
        $is_direct_sale = false;
        if (!empty($request->input('is_direct_sale'))) {
            $is_direct_sale = true;
        }

        if (!auth()->user()->can('sell.create') && !auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $products_override = array();
        $sum_laba = 0;
        $sum_harga_pokok = 0;

        $group_price= $request->hidden_price_group != null ? $request->hidden_price_group : $request->default_price_group;

        foreach ($request->products as $key => $value) {
            $variation = Variation::with(['group_prices'=>function($q) use($group_price){
                $q->where('price_group_id',$group_price);
           }])->find($value['variation_id']);

            $length_group_price = count($variation->group_prices);
            $harga_jual= 0;
            $harga_input = $value['unit_price_inc_tax'];
            $harga_default = $variation['default_sell_price'];

            if($length_group_price > 0){
                $harga_jual = $variation->group_prices[0]->price_inc_tax;
            }elseif($harga_input != $harga_default){
                $harga_jual = $this->productUtil->num_uf($harga_input);
            //   $harga_jual = $harga_input;
            }else{
                $harga_jual = $harga_default;
            }

            $harga_pokok = $variation['default_purchase_price'];
            $laba = $harga_jual - $harga_pokok;

            $products_override[] = [ "unit_price" => $value['unit_price'],
                                    "line_discount_type" => $value['line_discount_type'],
                                    "line_discount_amount" => $value['line_discount_amount'],
                                    "item_tax" => $value['item_tax'],
                                    "tax_id" => $value['tax_id'],
                                    "sell_line_note" => $value['sell_line_note'],
                                    "product_id" => $value['product_id'],
                                    "variation_id" => $value['variation_id'],
                                    "enable_stock" => $value['enable_stock'],
                                    "quantity" => $value['quantity'],
                                    "unit_price_inc_tax" => $value['unit_price_inc_tax'],
                                    "harga_pokok"=>$harga_pokok,
                                    "laba"=>$laba
                                ];
            $sum_laba += $laba * $value['quantity'];
            $sum_harga_pokok += $harga_pokok * $value['quantity'];
        }

        try{
            $input = $request->except('_token');

            $depe_po = $this->transactionUtil->num_uf($input['depe_po']);
            
            $bayar_dp = $depe_po;
            // $bayar_dp = $depe_po <= $sum_harga_pokok ? $depe_po : $sum_harga_pokok;
            // $bayar_laba = $depe_po > $sum_harga_pokok ? $depe_po - $sum_harga_pokok : 0;
            // $cek['bayar_dp'] = $bayar_dp;
            // $cek['total_nota'] = $input['final_total'];
            // $cek['hpp_total'] = $sum_harga_pokok;
            // $cek['laba_total'] = $sum_laba;
            // dd($cek);
            if (empty($input['status'])) {
                $input['status'] = 'ordered';
            }

            if (!empty($input['products'])) {
                $business_id = $request->session()->get('user.business_id');
                

                if (!$this->moduleUtil->isSubscribed($business_id)) {
                    return $this->moduleUtil->expiredResponse();
                } elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
                    return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action('SellPosController@index'));
                }

                $user_id = $request->session()->get('user.id');
                $commsn_agnt_setting = $request->session()->get('business.sales_cmsn_agnt');
                $discount = ['discount_type' => $input['discount_type'],
                                'discount_amount' => $input['discount_amount']
                            ];
                $invoice_total = $this->productUtil->calculateInvoiceTotal($input['products'], $input['tax_rate_id'], $discount);
                
                DB::beginTransaction();

                if (empty($request->input('transaction_date'))) {
                    $input['transaction_date'] =  \Carbon::now();
                } else {
                    $input['transaction_date'] = $this->productUtil->uf_date($request->input('transaction_date'), true);
                }

                if ($is_direct_sale) {
                    $input['is_direct_sale'] = 1;
                }

                $input['commission_agent'] = !empty($request->input('commission_agent')) ? $request->input('commission_agent') : null;
                if ($commsn_agnt_setting == 'logged_in_user') {
                    $input['commission_agent'] = $user_id;
                }

                if(isset($input['exchange_rate']) && $this->transactionUtil->num_uf($input['exchange_rate']) == 0){
                    $input['exchange_rate'] = 1;
                }

                //Customer group details
                $contact_id = $request->get('contact_id', null);
                $cg = $this->contactUtil->getCustomerGroup($business_id, $contact_id);
                $input['customer_group_id'] = (empty($cg) || empty($cg->id)) ? null : $cg->id;

                //set selling price group id
                if($request->has('price_group')){
                    $input['selling_price_group_id'] = $request->input('price_group');
                }

                if(isset($request->is_hutang_piutang)){
                    $input['is_hutang_piutang'] = 1;
                }

                $bayar = isset($request->bayar) ? $request->bayar : null;
                $kembali = isset($request->kembali) ? $request->kembali : null;

                $input['bayar'] = $this->transactionUtil->num_uf($bayar);
                $input['kembali'] = $this->transactionUtil->num_uf($kembali);
                // $input['invoice_no'] = 
                // dd($input);
                $transaction = $this->transactionUtil->createSellTransaction($business_id, $input, $invoice_total, $user_id);

                $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id']);

                // pembayaran DP di sini
                $ref_count_1 = $this->transactionUtil->setAndGetReferenceCount('sell_payment', $business_id);
                $payment_ref_no_1 = $this->transactionUtil->generateReferenceNumber('sell_payment', $ref_count_1, $business_id);
                
                $method = $request->payment[0]['method'];
                $rek_debit_dp = $method == 'cash' ? '111.95' : '121.26';
                $rek_kredit_dp = $method == 'cash' ? '211.08' : '211.07';


                $bayar_dp_preorder = [
                    'transaction_id' => $transaction->id,
                    'amount' => $bayar_dp,
                    'method' => $method,
                    'business_id' => $business_id,
                    'is_return' => isset($request->payment[0]['is_return']) ? $request->payment[0]['is_return'] : 0,
                    'card_transaction_number' => $request->payment[0]['card_transaction_number'],
                    'card_number' => $request->payment[0]['card_number'],
                    'card_type' => $request->payment[0]['card_type'],
                    'card_holder_name' => $request->payment[0]['card_holder_name'],
                    'card_month' => $request->payment[0]['card_month'],
                    'card_security' => $request->payment[0]['card_security'],
                    'cheque_number' => $request->payment[0]['cheque_number'],
                    'bank_account_number' => $request->payment[0]['bank_account_number'],
                    'note'  => $request->payment[0]['note'],
                    'paid_on' => !empty($input['transaction_date']) ? $input['transaction_date'] : \Carbon::now()->toDateTimeString(),
                    'created_by' => empty($user_id) ? auth()->user()->id : $user_id,
                    'payment_for' => $transaction->contact_id,
                    'payment_ref_no' => $payment_ref_no_1,
                    'account_id'   => !empty($request->payment[0]['account_id']) ? $request->payment[0]['account_id'] : null,
                    'id_rekening_debit'  => $rek_debit_dp,
                    'id_rekening_kredit'  => $rek_kredit_dp
                ];

                // dd($bayar_dp_preorder);
                if($bayar_dp > 0){
                    TransactionPayment::create($bayar_dp_preorder);
                }


                $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total, $transaction->shipping_charges,'sell');
                
                DB::commit();

                $msg = '';
                $receipt = '';
                $msg = "Pre-Order Berhasil Ditambahkan";
                $receipt = $this->fakturPreOrder($business_id, $input['location_id'], $transaction->id);
                
                $output = ['success' => 1, 'msg' => $msg, 'receipt' => $receipt ];

            }else{
                $output = ['success' => 0,
                            'msg' => trans("messages.something_went_wrong")
                        ];
            }

        } catch(\Exception $e){
            DB::rollBack();

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            } else {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $msg = trans("messages.something_went_wrong");
            }

            $output = ['success' => 0,
                            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
        }
        return redirect('preorder')->with('status', $output);
    }



    public function storePayPreOrder(Request $request)
    {

        $sum_laba = 0;
        $sum_harga_pokok = 0;
        
        $data = $request->except('_token');
        $business_id = $request->session()->get('user.business_id');
        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);

        
        $group_price= $request->hidden_price_group != null ? $request->hidden_price_group : 
        $walk_in_customer['selling_price_group_id'];

        $products = TransactionSellLine::join(
            'products',
            'transaction_sell_lines.product_id',
            '=',
            'products.id'
        )
        ->where('transaction_sell_lines.transaction_id', $data['transaction_id'])
        ->select(
            'transaction_sell_lines.transaction_id',
            'transaction_sell_lines.unit_price',
            'transaction_sell_lines.product_id',
            'transaction_sell_lines.variation_id',
            'products.enable_stock',
            'transaction_sell_lines.unit_price_inc_tax',
            'transaction_sell_lines.line_discount_type',
            'transaction_sell_lines.line_discount_amount',
            'transaction_sell_lines.quantity'
        )
        ->get()->toArray();

        $products_override = array();

        foreach ($products as $key => $value) {
            $variation = Variation::with(['group_prices'=>function($q) use($group_price){
                $q->where('price_group_id',$group_price);
           }])->find($value['variation_id']);

           $length_group_price = count($variation->group_prices);
           $harga_jual=0;
           $harga_input = $value['unit_price_inc_tax'];
           $harga_default = $variation['default_sell_price'];

            if($length_group_price > 0){
                $harga_jual = $variation->group_prices[0]->price_inc_tax;
            }elseif($harga_input != $harga_default){
                $harga_jual = $this->productUtil->num_f($harga_input);
            }else{
                $harga_jual = $harga_default;
            }

            $harga_pokok = $variation['default_purchase_price'];
            $laba = $harga_jual - $harga_pokok;

            $sum_laba += $laba * $value['quantity'];
            $sum_harga_pokok += $harga_pokok * $value['quantity'];

            $products_override[] = [
                                    "unit_price" => $value['unit_price'],
                                    "line_discount_type" => $value['line_discount_type'],
                                    "line_discount_amount" => $value['line_discount_amount'],
                                    // "item_tax" => $value['item_tax'], 
                                    // "tax_id" => $value['tax_id'],
                                    // "sell_line_note" => $value['sell_line_note'],
                                    "product_id" => $value['product_id'],
                                    "variation_id" => $value['variation_id'],
                                    "enable_stock" => $value['enable_stock'],
                                    "quantity" => $value['quantity'],
                                    "unit_price_inc_tax" => $value['unit_price_inc_tax'],
                                    "harga_pokok"=>$harga_pokok,
                                    "laba"=>$laba
                                ];
        }


        $data_bayar = $this->bayarPreOrder($data['transaction_id']);
        // dd($data_bayar);
        $final_total = (float)$data_bayar['final_total'];
        $total_dp_po = (float)$data_bayar['total_bayar_po'];
        $bayar_pelunasan = $this->productUtil->num_uf($data['amount']);
        $total_bayar = $total_dp_po + $bayar_pelunasan;
        $rek_mutasi_hpp_po = $total_dp_po <= $sum_harga_pokok ? $total_dp_po : $sum_harga_pokok;

        // Rumus Laba Pre-Order
        // Jika DP lebih besar
        $laba_lebih_besar = $total_dp_po >= $sum_harga_pokok ? $total_dp_po - $sum_harga_pokok : 0;
        // $laba_lebih_besar = $total_dp_po >= $sum_harga_pokok ? ($total_dp_po - $sum_harga_pokok) + $bayar_pelunasan : 0;
        // Jika DP lebih kecil
        $laba_lebih_kecil = $total_bayar >= $sum_harga_pokok ? $total_bayar - $sum_harga_pokok : 0;
        // hasil akhir laba
        $rek_mutasi_laba_po = $total_dp_po >= $sum_harga_pokok ? $laba_lebih_besar : 0;

        $rek_pelunasan_laba = $total_dp_po >= $sum_harga_pokok ? $bayar_pelunasan : $laba_lebih_kecil ;
        // Rumus Hpp Pre-Order
        $rek_pelunasan_hpp = $total_dp_po < $sum_harga_pokok ? $bayar_pelunasan - $rek_pelunasan_laba : 0;

        $hasil_hitung['total_invoice'] = $final_total;
        $hasil_hitung['depe_pre_order'] = $total_dp_po;
        $hasil_hitung['bayar_pelunasan'] = $bayar_pelunasan;
        $hasil_hitung['total_bayar'] = $total_bayar;
        $hasil_hitung['mutasi_hpp_pre_order'] = $rek_mutasi_hpp_po;
        $hasil_hitung['pelunasan_hpp'] = $rek_pelunasan_hpp;
        $hasil_hitung['mutasi_laba_pre_order'] = $rek_mutasi_laba_po;
        $hasil_hitung['pelunasan_laba'] = $rek_pelunasan_laba;

        // dd($hasil_hitung);
        if (!auth()->user()->can('sell.create') && !auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }
        try{
            
            $transaction = Transaction::where('business_id', $business_id)->findOrFail($data['transaction_id']);
            
            if ($transaction->status == 'ordered') {
                $transaction->status = 'final';
            }
            
            if ($transaction->payment_status != 'paid') {
                
                $inputs = $request->only(['amount', 'method', 'note', 'card_number', 'card_holder_name',
                'card_transaction_number', 'card_type', 'card_month', 'card_year', 'card_security',
                'cheque_number', 'bank_account_number']);
                // dd($inputs);

                if($inputs['method'] == 'cash'){
                    // pembayaran kas
                    // rekening mutasi hpp pre-order
                    $rek_debit_mutasi_hpp = '211.09';
                    $rek_kredit_mutasi_hpp = '131.13';

                    // rekening pelunasan hpp pre-order
                    $rek_debit_hpp_po = '111.106';
                    $rek_kredit_hpp_po = '131.14';

                    // rekening mutasi laba pre-order
                    $rek_debit_mutasi_laba = '211.10';
                    $rek_kredit_mutasi_laba = '411.19';

                    // rekening pelunasan laba pre-order
                    $rek_debit_laba_po = '111.107';
                    $rek_kredit_laba_po = '411.17';
                
                }else{
                    // pembayaran bank
                    // rekening mutasi hpp pre-order
                    $rek_debit_mutasi_hpp = '211.09';
                    $rek_kredit_mutasi_hpp = '131.13';

                    // rekening pelunasan hpp pre-order
                    $rek_debit_hpp_po = '121.29';
                    $rek_kredit_hpp_po = '131.15';

                    // rekening mutasi labapre-order
                    $rek_debit_mutasi_laba = '211.10';
                    $rek_kredit_mutasi_laba = '411.19';

                    // rekening pelunasan laba pre-order
                    $rek_debit_laba_po = '121.30';
                    $rek_kredit_laba_po = '411.18';
                }
                // dd($transaction);
                
                DB::beginTransaction();

                // $hasil_hitung['bayar_pelunasan'] = $bayar_mutasi_hpp_po;
                // $hasil_hitung['pelunasan_hpp_po'] = $pelunasan_hpp_po;
                // $hasil_hitung['bayar_mutasi_laba_po'] = $bayar_mutasi_laba_po;

                if($rek_mutasi_hpp_po > 0){
                    $ref_count_1 = $this->transactionUtil->setAndGetReferenceCount('sell_payment', $business_id);
                    $payment_ref_no_1 = $this->transactionUtil->generateReferenceNumber('sell_payment', $ref_count_1, $business_id);

                    $bayar_mutasi_hpp_po = [
                        'transaction_id' => $transaction->id,
                        'amount' => $rek_mutasi_hpp_po,
                        'method' => $inputs['method'],
                        'business_id' => $business_id,
                        'is_return' => isset($request->payment[0]['is_return']) ? $request->payment[0]['is_return'] : 0,
                        'card_transaction_number' => $inputs['card_transaction_number'],
                        'card_number' => $inputs['card_number'],
                        'card_type' => $inputs['card_type'],
                        'card_holder_name' => $inputs['card_holder_name'],
                        'card_month' => $inputs['card_month'],
                        'card_security' => $inputs['card_security'],
                        'cheque_number' => $inputs['cheque_number'],
                        'bank_account_number' => $inputs['bank_account_number'],
                        'note'  => $inputs['note'],
                        'paid_on' => \Carbon::createFromFormat('m/d/Y', $data['paid_on'])->toDateTimeString(),
                        'created_by' => empty($user_id) ? auth()->user()->id : $user_id,
                        'payment_for' => $transaction->contact_id,
                        'payment_ref_no' => $payment_ref_no_1,
                        'account_id'   => !empty($request->payment[0]['account_id']) ? $request->payment[0]['account_id'] : null,
                        'id_rekening_debit'  => $rek_debit_mutasi_hpp,
                        'id_rekening_kredit'  => $rek_kredit_mutasi_hpp
                    ];

                    TransactionPayment::create($bayar_mutasi_hpp_po);
                }

                if($rek_pelunasan_hpp > 0){
                    $ref_count_2 = $this->transactionUtil->setAndGetReferenceCount('sell_payment', $business_id);
                    $payment_ref_no_2 = $this->transactionUtil->generateReferenceNumber('sell_payment', $ref_count_2, $business_id);

                    $pelunasan_hpp_po = [
                        'transaction_id' => $transaction->id,
                        'amount' => $rek_pelunasan_hpp,
                        'method' => $inputs['method'],
                        'business_id' => $business_id,
                        'is_return' => isset($request->payment[0]['is_return']) ? $request->payment[0]['is_return'] : 0,
                        'card_transaction_number' => $inputs['card_transaction_number'],
                        'card_number' => $inputs['card_number'],
                        'card_type' => $inputs['card_type'],
                        'card_holder_name' => $inputs['card_holder_name'],
                        'card_month' => $inputs['card_month'],
                        'card_security' => $inputs['card_security'],
                        'cheque_number' => $inputs['cheque_number'],
                        'bank_account_number' => $inputs['bank_account_number'],
                        'note'  => $inputs['note'],
                        'paid_on' => \Carbon::createFromFormat('m/d/Y', $data['paid_on'])->toDateTimeString(),
                        'created_by' => empty($user_id) ? auth()->user()->id : $user_id,
                        'payment_for' => $transaction->contact_id,
                        'payment_ref_no' => $payment_ref_no_2,
                        'account_id'   => !empty($request->payment[0]['account_id']) ? $request->payment[0]['account_id'] : null,
                        'id_rekening_debit'  => $rek_debit_hpp_po,
                        'id_rekening_kredit'  => $rek_kredit_hpp_po
                    ];

                    TransactionPayment::create($pelunasan_hpp_po);
                }

                if($rek_mutasi_laba_po > 0){
                    $ref_count_3 = $this->transactionUtil->setAndGetReferenceCount('sell_payment', $business_id);
                    $payment_ref_no_3 = $this->transactionUtil->generateReferenceNumber('sell_payment', $ref_count_3, $business_id);

                    $bayar_mutasi_laba_po = [
                        'transaction_id' => $transaction->id,
                        'amount' => $rek_mutasi_laba_po,
                        'method' => $inputs['method'],
                        'business_id' => $business_id,
                        'is_return' => isset($request->payment[0]['is_return']) ? $request->payment[0]['is_return'] : 0,
                        'card_transaction_number' => $inputs['card_transaction_number'],
                        'card_number' => $inputs['card_number'],
                        'card_type' => $inputs['card_type'],
                        'card_holder_name' => $inputs['card_holder_name'],
                        'card_month' => $inputs['card_month'],
                        'card_security' => $inputs['card_security'],
                        'cheque_number' => $inputs['cheque_number'],
                        'bank_account_number' => $inputs['bank_account_number'],
                        'note'  => $inputs['note'],
                        'paid_on' => \Carbon::createFromFormat('m/d/Y', $data['paid_on'])->toDateTimeString(),
                        'created_by' => empty($user_id) ? auth()->user()->id : $user_id,
                        'payment_for' => $transaction->contact_id,
                        'payment_ref_no' => $payment_ref_no_3,
                        'account_id'   => !empty($request->payment[0]['account_id']) ? $request->payment[0]['account_id'] : null,
                        'id_rekening_debit'  => $rek_debit_mutasi_laba,
                        'id_rekening_kredit'  => $rek_kredit_mutasi_laba
                    ];
                    TransactionPayment::create($bayar_mutasi_laba_po);
                }

                if($rek_pelunasan_laba > 0){
                    $ref_count_4 = $this->transactionUtil->setAndGetReferenceCount('sell_payment', $business_id);
                    $payment_ref_no_4 = $this->transactionUtil->generateReferenceNumber('sell_payment', $ref_count_4, $business_id);

                    $pelunasan_laba_po = [
                        'transaction_id' => $transaction->id,
                        'amount' => $rek_pelunasan_laba,
                        'method' => $inputs['method'],
                        'business_id' => $business_id,
                        'is_return' => isset($request->payment[0]['is_return']) ? $request->payment[0]['is_return'] : 0,
                        'card_transaction_number' => $inputs['card_transaction_number'],
                        'card_number' => $inputs['card_number'],
                        'card_type' => $inputs['card_type'],
                        'card_holder_name' => $inputs['card_holder_name'],
                        'card_month' => $inputs['card_month'],
                        'card_security' => $inputs['card_security'],
                        'cheque_number' => $inputs['cheque_number'],
                        'bank_account_number' => $inputs['bank_account_number'],
                        'note'  => $inputs['note'],
                        'paid_on' => \Carbon::createFromFormat('m/d/Y', $data['paid_on'])->toDateTimeString(),
                        'created_by' => empty($user_id) ? auth()->user()->id : $user_id,
                        'payment_for' => $transaction->contact_id,
                        'payment_ref_no' => $payment_ref_no_4,
                        'account_id'   => !empty($request->payment[0]['account_id']) ? $request->payment[0]['account_id'] : null,
                        'id_rekening_debit'  => $rek_debit_laba_po,
                        'id_rekening_kredit'  => $rek_kredit_laba_po
                    ];

                    TransactionPayment::create($pelunasan_laba_po);
                }

                if($transaction->status == 'final'){
                    foreach ($products as $product) {
                        if ($product['enable_stock']) {
                            $this->productUtil->decreaseProductQuantity(
                                $product['product_id'],
                                $product['variation_id'],
                                $transaction->location_id,
                                $this->productUtil->num_uf($product['quantity'])
                            );
                        }
                        
                    }
                    $transaction->status = 'final';
                    $transaction->save();

                    $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total, $transaction->shipping_charges,'sell');

                    $business = [
                                    'id' => $business_id,
                                    'accounting_method' => $request->session()->get('business.accounting_method'),
                                    'location_id' => $transaction->location_id
                                ];
                    
                    $this->transactionUtil->mapPurchaseSell($business, $transaction->sell_lines, 'purchase');
                }

                DB::commit();


                
            }

            $output = ['success' => true,
                            'msg' => __('purchase.payment_added_success')
                        ];

        } catch(\Exception $e){
            DB::rollBack();

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            } else {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $msg = trans("messages.something_went_wrong");
            }
            $output = ['success' => 0,
                            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
        }

        return $output;
    }

    public function show($id){
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

       
        $business_id = request()->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
                            ->pluck('name', 'id');

        $is_hutang_piutang = Transaction::select('is_hutang_piutang')->where('id',$id)->first()->is_hutang_piutang;

        $sell = Transaction::where('business_id', $business_id)
                    ->where('id', $id)
                    ->with(['contact', 'sell_lines' => function ($q) {
                        $q->whereNull('parent_sell_line_id');
                    },'sell_lines.product', 'sell_lines.variations', 'sell_lines.variations.product_variation', 'sell_lines.modifiers', 'sell_lines.lot_details', 'tax'])
                    ->with(array('payment_lines'=>function($query)use($is_hutang_piutang, $business_id){
                            $query->leftJoin('rekening','transaction_payments.id_rekening_debit','=','rekening.kd_rekening')
                            ->where('rekening.business_id', $business_id);
                            $query->groupBy('transaction_payments.id');
                        if($is_hutang_piutang == 1){
                            $query->whereRaw('LEFT(id_rekening_debit,2) != 51');
                            $query->whereNotIn('id_rekening_kredit',['131.08','411.04','411.02','131.13','411.19']);
                        }
                    }))
                    ->first();

        $payment_types = $this->transactionUtil->payment_types();

        $order_taxes = [];
        if(!empty($sell->tax)){
            if ($sell->tax->is_tax_group) {
                $order_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->tax, $sell->tax_amount));
            } else {
                $order_taxes[$sell->tax->name] = $sell->tax_amount;
            }
        }

        return view('preorder.show')
            ->with(compact('taxes', 'sell', 'payment_types', 'order_taxes'));
    }

    public function printPreOrder(Request $request, $transaction_id){
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

                $receipt = $this->fakturPreOrder($business_id, $transaction->location_id, $transaction_id, 'browser');

                if (!empty($receipt)) {
                    $output = ['success' => 1, 'receipt' => $receipt];
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                $output = ['success' => 0,
                        'msg' => trans("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }


    private function fakturPreOrder(
        $business_id,
        $location_id,
        $transaction_id,
        $printer_type = null
    ){
        $output = ['is_enabled' => false,
                    'print_type' => 'browser',
                    'html_content' => null,
                    'printer_config' => [],
                    'data' => []
                ];

        $business_details = $this->businessUtil->getDetails($business_id);
        $location_details = BusinessLocation::find($location_id);
                // dd($business_details);
        
        
            //Check if printing of invoice is enabled or not.
        if ($location_details->print_receipt_on_invoice == 1) {
            //If enabled, get print type.
            $output['is_enabled'] = true;
        
            $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $location_id, $location_details->invoice_layout_id);
        
            //Check if printer setting is provided.
            $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;
        
            $receipt_details = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details,$receipt_printer_type);
        // dd($receipt_details);
            $receipt_details->currency = session('currency');
                    
            //If print type browser - return the content, printer - return printer config data, and invoice format config
            if ($receipt_printer_type == 'printer') {
                $output['print_type'] = 'printer';
                $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
                $output['data'] = $receipt_details;
            } else {
                // $layout = !empty($receipt_details->design) ? 'sale_pos.receipts.' . $receipt_details->design : 'sale_pos.receipts.classic';
                // $output['html_content'] = view('sale_pos.receipts.close_pos', compact('receipt_details'))->render();
                // dd($output);
                $output['html_content'] = view('preorder.partials.faktur_pre_order', compact('receipt_details'))->render();
            }
        }
                
        return $output;
    }

    private function receiptContentFaktur(
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
        // dd($business_details);


        //Check if printing of invoice is enabled or not.
        if ($location_details->print_receipt_on_invoice == 1) {
            //If enabled, get print type.
            $output['is_enabled'] = true;

            $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $location_id, $location_details->invoice_layout_id);

            //Check if printer setting is provided.
            $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;

            $receipt_details = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details,$receipt_printer_type);
// dd($receipt_details);
            $receipt_details->currency = session('currency');
            
            
            //If print type browser - return the content, printer - return printer config data, and invoice format config
            if ($receipt_printer_type == 'printer') {
                $output['print_type'] = 'printer';
                $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
                $output['data'] = $receipt_details;
            } else {
                // $layout = !empty($receipt_details->design) ? 'sale_pos.receipts.' . $receipt_details->design : 'sale_pos.receipts.classic';
                // $output['html_content'] = view('sale_pos.receipts.close_pos', compact('receipt_details'))->render();
                // dd($output);
                $output['html_content'] = view('sale_pos.receipts.faktur', compact('receipt_details'))->render();
            }
        }
        
        return $output;
    }

    public function getProductRow($variation_id, $location_id)
    {
        $output = [];

        try {
            $row_count = request()->get('product_row');
            $row_count = $row_count + 1;
            $is_direct_sell = false;
            if (request()->get('is_direct_sell') == 'true') {
                $is_direct_sell = true;
            }

            $business_id = request()->session()->get('user.business_id');

            $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id, false);
            $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available);

            //Get customer group and change the price accordingly
            $customer_id = request()->get('customer_id', null);
            $cg = $this->contactUtil->getCustomerGroup($business_id, $customer_id);
            $percent = (empty($cg) || empty($cg->amount)) ? 0 : $cg->amount;
            $product->default_sell_price = $product->default_sell_price + ($percent * $product->default_sell_price / 100);
            $product->sell_price_inc_tax = $product->sell_price_inc_tax + ($percent * $product->sell_price_inc_tax / 100);

            $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);

            $enabled_modules = $this->transactionUtil->allModulesEnabled();

            //Get lot number dropdown if enabled
            $lot_numbers = array();
            if(request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1){
                $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($variation_id, $business_id, $location_id, true);
                foreach ($lot_number_obj as $lot_number) {
                    $lot_number->qty_formated = $this->productUtil->num_f($lot_number->qty_available);
                    $lot_numbers[] = $lot_number;
                }
            }
            $product->lot_numbers = $lot_numbers;

            $price_group = request()->input('price_group');
            if(!empty($price_group)){
                $variation_group_prices = $this->productUtil->getVariationGroupPrice($variation_id, $price_group, $product->tax_id);
                
                if(!empty($variation_group_prices['price_inc_tax'])){
                    $product->sell_price_inc_tax = $variation_group_prices['price_inc_tax'];
                    $product->default_sell_price = $variation_group_prices['price_exc_tax'];  
                }
            }

            $business_details = $this->businessUtil->getDetails($business_id);
            $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

            $output['success'] = true;
            if (request()->get('type') == 'sell-return') {
                $output['html_content'] =  view('sell_return.partials.product_row')
                            ->with(compact('product', 'row_count', 'tax_dropdown', 'enabled_modules'))
                            ->render();
            } else {
                $output['html_content'] =  view('preorder.partials.product_row')
                            ->with(compact('product', 'row_count', 'tax_dropdown', 'enabled_modules', 'pos_settings'))
                            ->render();
            }
            
            $output['enable_sr_no'] = $product->enable_sr_no;

            if ($this->transactionUtil->isModuleEnabled('modifiers')  && !$is_direct_sell) {
                $this_product = Product::where('business_id', $business_id)
                                        ->find($product->product_id);
                if (count($this_product->modifier_sets) > 0) {
                    $product_ms = $this_product->modifier_sets;
                    $output['html_modifier'] =  view('restaurant.product_modifier_set.modifier_for_product')
                    ->with(compact('product_ms', 'row_count'))->render();
                }
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output['success'] = false;
            $output['msg'] = __('lang_v1.item_out_of_stock');
        }

        return $output;
    }

    public function checkPayment($transaction_id)
    {
        $transaction = Transaction::findOrFail($transaction_id);

        $getpayments = TransactionPayment::where('transaction_id',$transaction_id)
                                      ->selectRaw("SUM(COALESCE(amount,0)) as total_payment");
        if($transaction->is_hutang_piutang == 1){
            $getpayments->whereNotIn('id_rekening_kredit',['131.08','411.04','411.02','131.13','411.19']);
            $getpayments->whereRaw("LEFT(id_rekening_debit,2) != 51");
        }
        $payments = $getpayments->first();

        $kekurangan = $transaction->final_total - $payments->total_payment;

        return $kekurangan;
    }


    public function bayarPreOrder($transaction_id)
    {
        $transaction = Transaction::findOrFail($transaction_id);

        $getpayments = TransactionPayment::where('transaction_id',$transaction_id)
                                      ->selectRaw("SUM(COALESCE(amount,0)) as total_payment, method");
        // if($transaction->is_hutang_piutang == 1){
        //     $getpayments->whereNotIn('id_rekening_kredit',['131.08','411.04','411.02']);
        //     $getpayments->whereRaw("LEFT(id_rekening_debit,2) != 51");
        // }
        // $getpayments->where('id_rekening_debit', '111.95');
        // $getpayments->orWhere('id_rekening_debit', '121.26');
        $getpayments->whereIn('id_rekening_debit',['111.95','121.26']);
        $payments = $getpayments->first();

        $kekurangan = $transaction->final_total - $payments->total_payment;
        // $result[] = [
        //     "final_total" => $transaction->final_total,
        //     "total_bayar_po" => $payments->total_payment
        // ];
        $result['final_total'] = $transaction->final_total;
        $result['total_bayar_po'] = $payments->total_payment;
        $result['method'] = $payments->method;

        return $result;
    }


    public function cancelPreOrder($id){

        if (!auth()->user()->can('sell.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            // asd
            try{

                $business_id = request()->session()->get('user.business_id');
                $transaction = Transaction::where('id', $id)
                            ->where('business_id', $business_id)
                            ->where('type', 'sell')
                            ->with(['sell_lines'])
                            ->first();
                            
                $data_bayar = $this->bayarPreOrder($id);
                // dd($data_bayar);
                $total_dp_po = (float)$data_bayar['total_bayar_po'];
                $method = $data_bayar['method'];

                DB::beginTransaction();

                // rekening pembatalan pre-order
                $rek_debit_batal = $method == 'cash' ? '211.11' : '211.12';
                $rek_kredit_batal = $method == 'cash' ? '111.108' : '121.36';
                

                $ref_count_1 = $this->transactionUtil->setAndGetReferenceCount('sell_payment', $business_id);
                $payment_ref_no_1 = $this->transactionUtil->generateReferenceNumber('sell_payment', $ref_count_1, $business_id);

                $batal_po = [
                    'transaction_id' => $transaction->id,
                    'amount' => $total_dp_po,
                    'method' => $method,
                    'business_id' => $business_id,
                    'is_return' => 0,
                    'card_transaction_number' => null,
                    'card_number' => null,
                    'card_type' => 'credit',
                    'card_holder_name' => null,
                    'card_month' => null,
                    'card_security' => null,
                    'cheque_number' => null,
                    'bank_account_number' => null,
                    'note'  => null,
                    'paid_on' => \Carbon::now()->format("Y-m-d H:i:s"),
                    'created_by' => empty($user_id) ? auth()->user()->id : $user_id,
                    'payment_for' => $transaction->contact_id,
                    'payment_ref_no' => $payment_ref_no_1,
                    'account_id'   => null,
                    'id_rekening_debit'  => $rek_debit_batal,
                    'id_rekening_kredit'  => $rek_kredit_batal
                ];

                TransactionPayment::create($batal_po);

                DB::commit();
                $output = [
                    'success' => true,
                    'msg' => "Pre-Order Dibatalkan"
                ];

            } catch (\Exception $e) {
                // sad
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output['success'] = false;
                $output['msg'] = trans("messages.something_went_wrong");
            }

            return $output;
        }

        // return "ok";
    }
}
