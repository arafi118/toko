<?php

namespace App\Http\Controllers;

use App\Transaction;

use Illuminate\Http\Request;

use App\BusinessLocation;
use App\PurchaseLine;
use App\SimpanBarang;
use App\StockAdjustmentLine;

use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\ModuleUtil;

use Datatables;
use DB;
use PDF;


use App\Product;
use App\Category;
use App\Brands;
use App\Unit;
use App\SellingPriceGroup;

class StockAdjustmentController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('purchase.view') && !auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->pluck('name', 'id');
            $stock_adjustments = Transaction::leftJoin(
                'business_locations AS BL',
                'transactions.location_id',
                '=',
                'BL.id'
            )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'stock_adjustment')
                    ->select(
                        'transactions.id',
                        'transaction_date',
                        'ref_no',
                        'BL.name as location_name',
                        'adjustment_type',
                        'final_total',
                        'total_amount_recovered',
                        'transactions.id as DT_RowId',
                        'additional_notes',
                        // 'sl.quantity as quantity',
                        'final_total as ft'
                    )
                    ->orderBy('transaction_date','desc');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $stock_adjustments->whereIn('transactions.location_id', $permitted_locations);
            }

            $hide = '';
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $stock_adjustments->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
                $hide = 'hide';
            }
            $location_id = request()->get('location_id');
            if (!empty($location_id)) {
                $stock_adjustments->where('location_id', $location_id);
            }
            
            return Datatables::of($stock_adjustments)
                ->addColumn('action', '<button type="button" title="{{__("stock_adjustment.view_details") }}" class="btn btn-primary btn-xs view_stock_adjustment"><i class="fa fa-eye-slash" aria-hidden="true"></i></button> &nbsp;
                    <button type="button" data-href="{{  action("StockAdjustmentController@destroy", [$id]) }}" class="btn btn-danger btn-xs delete_stock_adjustment ' . $hide . '"><i class="fa fa-trash" aria-hidden="true"></i> @lang("messages.delete")</button>
                    <button type="button" data-href="{{  action("StockAdjustmentController@print", [$id]) }}" class="btn btn-warning btn-xs print_stock_adjustment ' . $hide . '"><i aria-hidden="true"></i> @lang("messages.cetak")</button>')
                ->removeColumn('id')
                ->editColumn(
                    'final_total',
                    '<span class="display_currency" data-currency_symbol="true">{{$final_total}}</span>'
                )
                ->editColumn(
                    'total_amount_recovered',
                    '<span class="display_currency" data-currency_symbol="true">{{$total_amount_recovered}}</span>'
                )
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('adjustment_type', function ($row) {
                        return __('stock_adjustment.' . $row->adjustment_type);
                })
                ->rawColumns(['final_total', 'action', 'total_amount_recovered'])
                ->make(true);
        }

        return view('stock_adjustment.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $categories = Category::where('business_id', $business_id)
                            ->where('parent_id', 0)
                            ->pluck('name', 'id');
        $brands = Brands::where('business_id', $business_id)
                            ->pluck('name', 'id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('StockAdjustmentController@index'));
        }

        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('stock_adjustment.create')
                ->with(compact('business_locations', 'categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }
        
        // return response($request->all());

        try{
            DB::beginTransaction();
            $input_data = $request->only(['transaction_date', 'ref_no']);

            // dd($input_data);
            // dd($request->input('products'));
            
                $fintot = $request->input('final_total');
                $business_id = $request->session()->get('user.business_id');
                $location_id = DB::table('business_locations')->where('business_id', '=', $business_id)->first()->id;

                $user_id = $request->session()->get('user.id');

                $input_data['type'] = 'stock_adjustment';
                $input_data['business_id'] = $business_id;
                $input_data['created_by'] = $user_id;
                $input_data['location_id'] = $location_id;
                $input_data['final_total'] = "$fintot";
                $input_data['transaction_date'] = $this->productUtil->uf_date($input_data['transaction_date']);
                
                $ref_count = $this->productUtil->setAndGetReferenceCount('stock_adjustment');
                if (empty($input_data['ref_no'])) {
                    $input_data['ref_no'] = $this->productUtil->generateReferenceNumber('stock_adjustment', $ref_count);
                }
                $stock_nol = 0;
                if (!empty($request->input('products'))) {
             
                    // $stock_so = [];
                    $produk = $request->input('products');
                    $data_produk = collect($produk)->where('harga', '>', 0)->all();
                    
                    $stock_nol = collect($data_produk)->where('stock', '<=', 0)->all();
                    // dd($data_produk);
                    
                    $stock = collect($data_produk)->where('stock', '>', 0)->all();
                    $stock_adjustment = Transaction::create($input_data);
                    
                    $business = [   'id' => $business_id,
                                    'accounting_method' => $request->session()->get('business.accounting_method'),
                                    'location_id' => $input_data['location_id']
                                ];
                    //    $ajdust_2 = "blank";
                    // dd($getAdjust);
                    
                    // dd($produk);

                    $id_tx = $stock_adjustment->id;
                    if(!empty($stock_nol)){
                        
                        $trans_to_adjust = Transaction::find($id_tx);
                        // dd($trans);
                        $getAdjustNol = $this->getAdjustmentLine($stock_nol, $input_data['location_id']);
                        // $stock_adjustment_zero = $stock_adjustment;
                        $trans_to_adjust->stock_adjustment_lines()->createMany($getAdjustNol);
                        $ajdust_2 = $trans_to_adjust->stock_adjustment_lines;
                        // dd($ajdust_2);
                        
                        $this->transactionUtil->mapAdjustmentZero($business, $ajdust_2, 'stock_adjustment');
                    
                    }
                    
                    $trans_not_zero = Transaction::find($id_tx);
                    $getAdjust = $this->getAdjustmentLine($stock, $input_data['location_id']);
                    $trans_not_zero->stock_adjustment_lines()->createMany($getAdjust);
                    $ajdust_1 = $trans_not_zero->stock_adjustment_lines;
                    
                    // $array = compact('ajdust_2', 'ajdust_1');
                    // $dump_zero = collect($adjust_2)->where('quantity', '>', 0)->all();
                    // $dump = collect($adjust_1)->where()->('id', '!=', $)
                    // dd($trans_not_zero);
                    if(!empty($stock_nol)){
                        // $dump = "dump";
                        foreach ($ajdust_2 as $line_z) {
                            $line_filter = $ajdust_1->where('id', '!=', $line_z->id);
                        }
                    }else{
                        // $dump = "dimp";
                        $line_filter = $ajdust_1;
                    }
                    
                    // dd($dump);
                    $this->transactionUtil->mapPurchaseSell($business, $line_filter, 'stock_adjustment');
                    
                    
                }
            $output = ['success' => 1,
                'msg' => __('stock_adjustment.stock_adjustment_added_successfully')
            ];

            DB::commit();      
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }

        return redirect('stock-adjustments')->with('status', $output);
        // return response()->json($request->product_data);
        // echo "<pre>";
        // var_dump($request->data);
        // echo "</pre>";
    }
    
    
    public function getAdjustmentLine($ajdust_line, $location_id){
        $product_data = [];
        foreach ($ajdust_line as $key => $product) {
            if($product['unit_price'] > 0){
                $kurtam = "tambah";
                $qty_modif = $product['unit_price'];
            }else {
                $kurtam = "kurang";
                $qty_modif = $product['unit_price']*-1;
            }
            // $stock = $ajdust_line['stock'];

            $harga = $this->productUtil->num_uf($product['harga']);
            $harga_conv = "$harga";
            
            $rek_debit = '';
            $rek_kredit = '';
            $alasan_so = '';
            if($kurtam == 'kurang'){
                $alasan_so = $product['additional_notes'];
                $rek_debit = '513.01';
                $rek_kredit = '131.12';
                if($qty_modif == 0){
                    $alasan_so = '';
                    $rek_debit = '';
                    $rek_kredit = '';
                }
                
            }else{
                $alasan_so = $product['additional_notes'];
                $rek_debit = '131.11';
                $rek_kredit = '413.01';
            }

            $adjustment_line = [
                'product_id'    => $product['product_id'],
                'variation_id'  => $product['variation_id'],
                'quantity'      => $this->productUtil->num_uf($qty_modif),
                'qty_sblm'      => $product['stock'],
                'unit_price'    => $harga_conv,
                'kurang_tambah'  => $kurtam,
                'alasan_so'     => $alasan_so,
                // 'id_rekening_debit' => $kurtam == 'kurang' ? '513.01' : '131.11',
                // 'id_rekening_kredit'=> $kurtam == 'kurang' ? '131.12' : '413.01',
                'id_rekening_debit' => $rek_debit,
                'id_rekening_kredit'=> $rek_kredit,
                // 'see_so' => $so_adj,
            ];
            if(!empty($product['lot_no_line_id'])){
                //Add lot_no_line_id to stock adjustment line
                $adjustment_line['lot_no_line_id'] = $product['lot_no_line_id'];
            }
            $product_data[] = $adjustment_line;
            
            // dd($product_data);
            // $cek = "jajal";
            $this->productUtil->decreaseProductQuantity(
                $product['product_id'],
                $product['variation_id'],
                $location_id,
                $this->productUtil->num_uf($qty_modif),
                0,
                $kurtam
            );
        }
        // return compact('product_data', 'cek');
        return $product_data;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('purchase.view')) {
            abort(403, 'Unauthorized action.');
        }
        
        $stock_adjustment_details = Transaction::
                    join(
                        'stock_adjustment_lines as sl',
                        'sl.transaction_id',
                        '=',
                        'transactions.id'
                    )
                    ->join('products as p', 'sl.product_id', '=', 'p.id')
                    ->join('variations as v', 'sl.variation_id', '=', 'v.id')
                    ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                    ->join('variation_location_details as vld', 'p.id', '=', 'vld.product_id')
                    ->where('transactions.id', $id)
                    ->where('transactions.type', 'stock_adjustment')
                    ->leftjoin('purchase_lines as pl', 'sl.lot_no_line_id', '=', 'pl.id')
                    ->select(
                        'p.name as product',
                        'v.sub_sku',
                        'p.enable_stock as enable_stock',
                        'p.type as type',
                        'vld.qty_available as stock',
                        'additional_notes',
                        'pv.name as product_variation',
                        'v.name as variation',
                        'sl.quantity',
                        'sl.unit_price',
                        'sl.kurang_tambah',
                        'pl.lot_number',
                        'pl.exp_date'
                    )
                    ->groupBy('sl.id')
                    ->get();

        $lot_n_exp_enabled = false;
        if(request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1){
            $lot_n_exp_enabled = true;
        }

        return view('stock_adjustment.partials.details')
                ->with(compact('stock_adjustment_details', 'lot_n_exp_enabled'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transaction  $stockAdjustment
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $stockAdjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $stockAdjustment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $stockAdjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('purchase.delete')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            if (request()->ajax()) {
                DB::beginTransaction();

                $stock_adjustment = Transaction::where('id', $id)
                                    ->where('type', 'stock_adjustment')
                                    ->with(['stock_adjustment_lines'])
                                    ->first();

                //Add deleted product quantity to available quantity
                $stock_adjustment_lines = $stock_adjustment->stock_adjustment_lines;
                // dd($stock_adjustment_lines->id);
                if (!empty($stock_adjustment_lines)) {
                    $line_ids = [];
                    foreach ($stock_adjustment_lines as $stock_adjustment_line) {
                        // $this->productUtil->updateProductQuantity(
                        //     $stock_adjustment->location_id,
                        //     $stock_adjustment_line->product_id,
                        //     $stock_adjustment_line->variation_id,
                        //     $this->productUtil->num_f($stock_adjustment_line->quantity)
                        // );
                        $this->productUtil->decreaseAdjustmentQuantity(
                            $stock_adjustment_line->product_id,
                            $stock_adjustment_line->variation_id,
                            $stock_adjustment->location_id,
                            $this->productUtil->num_f($stock_adjustment_line->quantity),
                            0,
                            $stock_adjustment_line->kurang_tambah
                        );
                        $line_ids[] = $stock_adjustment_line->id;
                    }

                    // dd($line_ids);
                    $this->transactionUtil->mapPurchaseQuantityForDeleteStockAdjustment($line_ids);
                    // dd($cek);
                }
                $stock_adjustment->delete();

                //Remove Mapping between stock adjustment & purchase.

                $output = ['success' => 1,
                            'msg' => __('stock_adjustment.delete_success')
                        ];

                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }
        return $output;
    }

    /**
     * Return product rows
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getProductRow(Request $request)
    {
        if (request()->ajax()) {
            $row_index = $request->input('row_index');
            $variation_id = $request->input('variation_id');
            $location_id = $request->input('location_id');

            $business_id = $request->session()->get('user.business_id');
            $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id);
            $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available);

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
            
            return view('stock_adjustment.partials.product_table_row')
            ->with(compact('product', 'row_index'));
        }
    }

    /**
     * Sets expired purchase line as stock adjustmnet
     *
     * @param int $purchase_line_id
     * @return json $output
     */
    public function removeExpiredStock($purchase_line_id)
    {

        if (!auth()->user()->can('purchase.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $purchase_line = PurchaseLine::where('id', $purchase_line_id)
                                    ->with(['transaction'])
                                    ->first();

            if (!empty($purchase_line)) {
                DB::beginTransaction();

                $qty_unsold = $purchase_line->quantity - $purchase_line->quantity_sold - $purchase_line->quantity_adjusted - $purchase_line->quantity_returned;
                $final_total = $purchase_line->purchase_price_inc_tax * $qty_unsold;

                $user_id = request()->session()->get('user.id');
                $business_id = request()->session()->get('user.business_id');

                //Update reference count
                $ref_count = $this->productUtil->setAndGetReferenceCount('stock_adjustment');

                $stock_adjstmt_data = [
                    'type' => 'stock_adjustment',
                    'business_id' => $business_id,
                    'created_by' => $user_id,
                    'transaction_date' => \Carbon::now()->format('Y-m-d'),
                    'total_amount_recovered' => 0,
                    'location_id' => $purchase_line->transaction->location_id,
                    'adjustment_type' => 'normal',
                    'final_total' => $final_total,
                    'ref_no' => $this->productUtil->generateReferenceNumber('stock_adjustment', $ref_count)
                ];

                //Create stock adjustment transaction
                $stock_adjustment = Transaction::create($stock_adjstmt_data);

                $stock_adjustment_line = [
                    'product_id' => $purchase_line->product_id,
                    'variation_id' => $purchase_line->variation_id,
                    'quantity' => $qty_unsold,
                    'unit_price' => $purchase_line->purchase_price_inc_tax,
                    'removed_purchase_line' => $purchase_line->id
                ];

                //Create stock adjustment line with the purchase line
                $stock_adjustment->stock_adjustment_lines()->create($stock_adjustment_line);

                //Decrease available quantity
                $this->productUtil->decreaseProductQuantity(
                    $purchase_line->product_id,
                    $purchase_line->variation_id,
                    $purchase_line->transaction->location_id,
                    $qty_unsold
                );

                //Map Stock adjustment & Purchase.
                $business = ['id' => $business_id,
                                'accounting_method' => request()->session()->get('business.accounting_method'),
                                'location_id' => $purchase_line->transaction->location_id
                            ];
                $this->transactionUtil->mapPurchaseSell($business, $stock_adjustment->stock_adjustment_lines, 'stock_adjustment', false, $purchase_line->id);

                DB::commit();

                $output = ['success' => 1,
                            'msg' => __('lang_v1.stock_removed_successfully')
                        ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }
        return $output;
    }
    
    
    public function getStockAdjustment(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $row_index = $request->input('row_index');
        $bl = BusinessLocation::where('business_id','=',$business_id)->first();
        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
                                                ->get();
        $allowed_selling_price_group = false;
            $variation_id = $request->input('variation_id');
            $location_id = $request->input('location_id');
            $transaction_date = $request->input('transaction_date');
        $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id);
        foreach ($selling_price_groups as $selling_price_group) {
            if(auth()->user()->can('selling_price_group.' . $selling_price_group->id)){
                $allowed_selling_price_group = true;
                break;
            }
        }
        // ini_set('max_execution_time', 7200);
            $query = Product::where('products.business_id', $business_id)
                    ->leftjoin('units', 'products.unit_id', '=', 'units.id')
                    ->join('variation_location_details as vld', 'products.id', '=', 'vld.product_id')
                    // ->leftjoin('variation_group_prices as vgp', 'vld.variation_id', '=', 'vgp.variation_id')
                    // ->leftjoin('purchase_lines as pline', 'vld.variation_id', '=', 'pline.variation_id')
                    ->leftjoin('variations as V', function($join){
                        $join->on('products.id', '=', 'V.product_id')
                            ->where('products.type', 'single');
                    });

            // pdf
            $location = BusinessLocation::where([['business_id','=',$business_id], ['id', '=', $request->location_id]])
                                ->first();
            $categori = Category::where([['business_id','=', $business_id], ['parent_id','=', 0], ['id', '=', $request->category]])
                                ->first();
            // $brand = Brands::where([['business_id', $business_id], ['id', '=', $request->brand]])
            //                     ->first();
            
            $rak = SimpanBarang::where([['business_id', $business_id], ['id', '=', $request->rak_id]])
                                ->first();
                                
            $nama_produk = $request->input('nama_produk');
            $additional_notes = $request->input('additional_notes');

            // search filter & pdf
            $permitted_locations = auth()->user()->permitted_locations();
            $location_filter = '';

            if ($permitted_locations != 'all') {
                $query->whereIn('vld.location_id', $permitted_locations);

                $locations_imploded = implode(', ', $permitted_locations);
                $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";
            }

            if (!empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');

                $query->where('vld.location_id', $location_id);

                $location_filter .= "AND transactions.location_id=$location_id";
            }

            if (!empty($request->input('category'))) {
                $query->where('products.category_id', $request->input('category'));
            }
            if (!empty($request->input('sub_category'))) {
                $query->where('products.sub_category_id', $request->input('sub_category'));
            }
            // if (!empty($request->input('brand'))) {
            //     $query->where('products.brand_id', $request->input('brand'));
            // }
            if (!empty($request->input('rak_id'))) {
                $query->where('products.id_letak_brg', $request->input('rak_id'));
            }
            if (!empty($request->input('unit'))) {
                $query->where('products.unit_id', $request->input('unit'));
            }

            if (!empty($request->input('nama_produk'))) {
                $query->where('products.name','like' ,'%'.$request->input('nama_produk').'%');
            }
            // ambil data dari database
            $qproducts = $query->select(
                // DB::raw("(SELECT SUM(quantity) FROM transaction_sell_lines LEFT JOIN transactions ON transaction_sell_lines.transaction_id=transactions.id WHERE transactions.status='final' $location_filter AND
                //     transaction_sell_lines.product_id=products.id) as total_sold"),

                // DB::raw("(SELECT SUM(IF(transactions.type='sell', TSL.quantity - TSL.quantity_returned , -1* TPL.quantity) ) FROM transactions 
                //         LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id

                //         LEFT JOIN purchase_lines AS TPL ON transactions.id=TPL.transaction_id

                //         WHERE transactions.status='final' AND transactions.type='sell' $location_filter 
                //         AND (TSL.product_id=products.id OR TPL.product_id=products.id)) as total_sold"),
                // DB::raw("(SELECT SUM(IF(transactions.type='sell_transfer', TSL.quantity, 0) ) FROM transactions 
                //         LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                //         WHERE transactions.status='final' AND transactions.type='sell_transfer' $location_filter 
                //         AND (TSL.product_id=products.id)) as total_transfered"),
                // DB::raw("(SELECT SUM(IF(transactions.type='stock_adjustment', SAL.quantity, 0) ) FROM transactions 
                //         LEFT JOIN stock_adjustment_lines AS SAL ON transactions.id=SAL.transaction_id
                //         WHERE transactions.status='received' AND transactions.type='stock_adjustment' $location_filter 
                //         AND (SAL.product_id=products.id)) as total_adjusted"),
                DB::raw("SUM(vld.qty_available) as stock"),
                DB::raw("vld.variation_id as idv"),
                'products.id as pid',
                'sku',
                'products.name as product',
                'products.type',
                'units.short_name as unit',
                'products.enable_stock as enable_stock',
                'products.id as DT_RowId',
                'V.sell_price_inc_tax as unit_price',
                // 'vgp.price_inc_tax as harga',
                'V.dpp_inc_tax as last_purchased_price'
                // DB::raw("(SELECT purchase_price_inc_tax FROM purchase_lines WHERE 
                //         variation_id=V.id ORDER BY id DESC LIMIT 1) as last_purchased_price")
                // 'TPL.purchase_price_inc_tax as hbeli'
            )

            ->groupBy('products.id');
  
            $categories = Category::where('business_id', $business_id)
                            ->where('parent_id', 0)
                            ->pluck('name', 'id');
            $brands = Brands::where('business_id', $business_id)
                                ->pluck('name', 'id');
            $units = Unit::where('business_id', $business_id)
                                ->pluck('short_name', 'id');
            $rak_bar = SimpanBarang::where('business_id', $business_id)
                                ->pluck('tempat_simpan', 'id');
            $business_locations = BusinessLocation::forDropdown($business_id);
            
        if($request->input('print') || $request->input('pdf')){

            $products = $qproducts->get();
            if($request->input('pdf')){
                $pdf = PDF::loadView('stock_adjustment.stock_adjustment_laporan',['categories'=>$categories,
                                                                  'brands'=>$brands,
                                                                  'units'=>$units,
                                                                  'business_locations'=>$business_locations,
                                                                  'products'=>$products,
                                                                  'row_index'=>$row_index,
                                                                  'additional_notes' =>$additional_notes,
                                                                  'allowed_selling_price_group'=>$allowed_selling_price_group,
                                                                  'bl'=>$bl]);
                $pdf->setPaper('A4', 'landscape');
                return $pdf->stream('Laporan SO.pdf');

            }else{
                return view('stock_adjustment.stock_adjustment_form')
                    ->with(compact('categories', 'brands', 'units', 'business_locations','products', 'allowed_selling_price_group', 'row_index', 'bl','nama_produk', 'categori', 'brands', 'location', 'additional_notes','rak', 'transaction_date'));
            // echo "<pre>";
            // var_dump($request->additional_notes);
            // echo "</pre>";
            }

        }else{
            $products = $qproducts->paginate(20);
            // $purchase = $product->paginate(20);
            return view('stock_adjustment.create2')
                ->with(compact('categories', 'brands', 'units', 'rak_bar', 'business_locations','products','allowed_selling_price_group','bl', 'row_index'));
        }
        
    }
    
    public function print($id)
    {
        if (!auth()->user()->can('purchase.view') && !auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $bl = BusinessLocation::where('business_id','=',$business_id)->first();
        $products = Transaction::Join('business_locations AS BL','transactions.location_id', '=', 'BL.id')
                    ->join('stock_adjustment_lines as sl','sl.transaction_id','=','transactions.id')
                    ->join('products as p', 'sl.product_id', '=', 'p.id')
                    ->join('variations as v', 'sl.variation_id', '=', 'v.id')
                    ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                    ->join('variation_location_details as vld', 'p.id', '=', 'vld.product_id')
                    ->where('transactions.id', $id)
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'stock_adjustment')
                    ->select(
                        'transaction_date',
                        'ref_no',
                        'BL.name as location_name',
                        'v.sub_sku',
                        'p.name as product',
                        'vld.qty_available as stock',
                        'sl.quantity',
                        'sl.kurang_tambah'
                   )
                    ->groupBy('sl.id')
                    ->get();
        $lot_n_exp_enabled = false;
            if(request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1){
                $lot_n_exp_enabled = true;
            }

        $pdf = PDF::loadView('stock_adjustment.pdfprint', [
                'products' => $products,
                'lot_n_exp_enabled' => $lot_n_exp_enabled,
                'bl' => $bl,
        ]);
        $pdf->setPaper('a4', 'potrait');

        return $pdf->stream();
        // return view('.stock_adjustment.pdfprint')
        //     ->with(compact('products', 'lot_n_exp_enabled', 'bl'));


    }
    
    public function getDaftarSO(Request $request){

        if (!auth()->user()->can('purchase.view') && !auth()->user()->can('purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $query = StockAdjustmentLine::join(
                'transactions as t',
                'stock_adjustment_lines.transaction_id',
                '=',
                't.id')
                // ->join('variations as v', 'stock_adjustment_lines.product_id', '=', 'v.product_id')
                ->join('products as p', 'stock_adjustment_lines.product_id', '=', 'p.id')
                ->leftjoin('simpan_barang as sb', 'sb.id', '=', 'p.id_letak_brg')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'stock_adjustment');
                
            $rak_id = $request->get('rak_id');
            $tgl = $request->get('so_date');
            $so_date = \Carbon::createFromFormat('d-m-Y', $tgl)->format("Y-m-d");
            if (!empty($so_date) ) {
                $query->where(DB::raw('date(t.transaction_date)'), $so_date);
            }

            if(!empty($rak_id)){
                $query->where('sb.id', $rak_id);
            }

            $query->select(
                't.transaction_date as so_date',
                'p.name as product_name',
                'sb.tempat_simpan as rak',
                't.ref_no as ref_no'
            )
            ->orderBy('p.id', 'asc');

            return Datatables::of($query)
                ->editColumn('so_date', '{{@format_date($so_date)}}')
                ->make(true);
        }

        $rak_bar = SimpanBarang::where('business_id', $business_id)
                            ->pluck('tempat_simpan', 'id');
        return view('stock_adjustment.daftar_stock_opname')
            ->with(compact('rak_bar'));
    }
    
    public function printDaftarSO(Request $request){
        if (!auth()->user()->can('sell.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $bl = BusinessLocation::where('business_id','=',$business_id)->first();

        $query = StockAdjustmentLine::join(
            'transactions as t',
            'stock_adjustment_lines.transaction_id',
            '=',
            't.id'
            )
            ->join('variations as v', 'stock_adjustment_lines.product_id', '=', 'v.product_id')
            ->join('products as p', 'stock_adjustment_lines.product_id', '=', 'p.id')
            ->join('units as u', 'u.id', '=', 'p.unit_id')
            ->join('simpan_barang as sb', 'sb.id', '=', 'p.id_letak_brg')
            ->join('users as us', 't.created_by', '=', 'us.id')
            ->where('t.business_id', $business_id)
            ->where('t.type', 'stock_adjustment');

            $rak_id = $request->get('rak_id');
            $tgl = $request->get('tanggal_so');
            $so_date = \Carbon::createFromFormat('d-m-Y', $tgl)->format("Y-m-d");
            if (!empty($so_date) ) {
                $query->where(DB::raw('date(t.transaction_date)'), $so_date);
            }

            if(!empty($rak_id)){
                $query->where('sb.id', $rak_id);
            }


            $data_so = $query->select(
                't.transaction_date as so_date',
                'p.name as product_name',
                'sb.tempat_simpan as rak',
                't.ref_no as ref_no',
                'stock_adjustment_lines.unit_price as hpp',
                'stock_adjustment_lines.qty_sblm as qty_sblm',
                'stock_adjustment_lines.quantity as qty',
                'stock_adjustment_lines.kurang_tambah as kurtam',
                'u.short_name as satuan',
                'us.initial as inisial'
            )
            ->orderBy('p.id', 'asc')
            ->get();

            // $pdf = PDF::loadView('stock_adjustment.print_daftar_so', [
            //     'data_so' => $data_so,
            //     'bl' => $bl,
            //     'tanggal_so' => $tgl,
                
            // ]);
            // $pdf->setPaper('A4', 'potrait');
    
            // return $pdf->stream();
            
            return view('stock_adjustment.print_daftar_so')
                ->with(compact('data_so', 'bl', 'tgl'));
    }
}
