<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Transaction;
use App\VariationLocationDetails;
use App\Currency;

use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
use App\Utils\ProductUtil;

use Datatables;
use Charts;
use DB;

class HomeController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $transactionUtil;
    protected $productUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        ProductUtil $productUtil
    ) {
    
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!auth()->user()->can('dashboard.data')) {
            return view('home.index');
        }

        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));

        $currency = Currency::where('id', request()->session()->get('business.currency_id'))->first();
        
        //Chart for sells last 30 days
        $sells_last_30_days = $this->transactionUtil->getSellsLast30Days($business_id);
        $labels = [];
        $values = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = \Carbon::now()->subDays($i)->format('Y-m-d');

            $labels[] = date('j M Y', strtotime($date));

            if (!empty($sells_last_30_days[$date])) {
                $values[] = $sells_last_30_days[$date];
            } else {
                $values[] = 0;
            }
        }

        $sells_chart_1 = Charts::create('bar', 'highcharts')
                            ->title(' ')
                            ->template("material")
                            ->values($values)
                            ->labels($labels)
                            ->elementLabel(__('home.total_sells', ['currency' => $currency->code]));

        //Chart for sells this financial year
        $sells_this_fy = $this->transactionUtil->getSellsCurrentFy($business_id, $fy['start'], $fy['end']);

        $labels = [];
        $values = [];

        $months = [];
        $date = strtotime($fy['start']);
        $last   = date('m-Y', strtotime($fy['end']));

        do {
            $month_year = date('m-Y', $date);

            $month_number = date('m', $date);

            $labels[] = \Carbon::createFromFormat('m-Y', $month_year)
                            ->format('M-Y');
            $date = strtotime('+1 month', $date);

            if (!empty($sells_this_fy[$month_year])) {
                $values[] = $sells_this_fy[$month_year];
            } else {
                $values[] = 0;
            }
        } while ($month_year != $last);

        $sells_chart_2 = Charts::create('bar', 'highcharts')
                            ->title(__(' '))
                            ->template("material")
                            ->values($values)
                            ->labels($labels)
                            ->elementLabel(__(
                                'home.total_sells',
                                ['currency' => $currency->code]
                            ));

        return view('home.index', compact('date_filters', 'sells_chart_1', 'sells_chart_2'));
    }

    /**
     * Retrieves purchase details for a given time period.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchaseDetails()
    {
        if (request()->ajax()) {
            $start = request()->start;
            $end = request()->end;
            $business_id = request()->session()->get('user.business_id');

            $purchase_details = $this->transactionUtil->getPurchaseTotals($business_id, $start, $end);

            return $purchase_details;
        }
    }
    public function getProductPopuler()
    {
        if (request()->ajax()) {
            $start = request()->start_date;
            $end = request()->end_date;
            // $arr = [];
            // $arr['satu'] = $start;
            // $arr['dua'] = $end;
            // dd($arr);
            // $query = 
            $business_id = request()->session()->get('user.business_id');

            // $purchase_details = $this->transactionUtil->getPurchaseTotals($business_id, $start, $end);
            $produk_populer = $this->productUtil->getDataProdukPopuler($business_id, $start, $end);
            // dd($produk_populer);
            // return $purchase_details;
            return Datatables::of($produk_populer)
            ->editColumn('total_unit_sold', function ($row) {
                // $total_unit_sold = $row->total_unit_sold ? $row->total_unit_sold : 0 ;
                return (float)$row->total_unit_sold . ' ' . $row->unit;
            })
            ->editColumn('stock', function ($row) {
                // $stock = $row->total_unit_sold ? $row->total_unit_sold : 0 ;
                return (float)$row->stock . ' ' . $row->unit;
            })
            ->removeColumn('unit')
            ->make(true);
        }
    }


    /**
     * Retrieves sell details for a given time period.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSellDetails()
    {
        if (request()->ajax()) {
            $start = request()->start;
            $end = request()->end;
            $business_id = request()->session()->get('user.business_id');

            $sell_details = $this->transactionUtil->getSellTotals($business_id, $start, $end);
            
            return $sell_details;
        }
    }

    /**
     * Retrieves sell products whose available quntity is less than alert quntity.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductStockAlert()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $query = VariationLocationDetails::join(
                'product_variations as pv',
                'variation_location_details.product_variation_id',
                '=',
                'pv.id'
            )
                    ->join(
                        'variations as v',
                        'variation_location_details.variation_id',
                        '=',
                        'v.id'
                    )
                    ->join(
                        'products as p',
                        'variation_location_details.product_id',
                        '=',
                        'p.id'
                    )
                    ->leftjoin(
                        'business_locations as l',
                        'variation_location_details.location_id',
                        '=',
                        'l.id'
                    )
                    ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                    ->where('p.business_id', $business_id)
                    ->where('p.enable_stock', 1)
                    ->whereRaw('variation_location_details.qty_available <= p.alert_quantity');

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('variation_location_details.location_id', $permitted_locations);
            }

            $products = $query->select(
                'p.name as product',
                'p.type',
                'pv.name as product_variation',
                'v.name as variation',
                'l.name as location',
                'variation_location_details.qty_available as stock',
                'u.short_name as unit'
            )
                    ->groupBy('variation_location_details.id')
                    ->orderBy('stock', 'asc');

            return Datatables::of($products)
                ->editColumn('product', function ($row) {
                    if ($row->type == 'single') {
                        return $row->product;
                    } else {
                        return $row->product . ' - ' . $row->product_variation . ' - ' . $row->variation;
                    }
                })
                ->editColumn('stock', function ($row) {
                    $stock = $row->stock ? $row->stock : 0 ;
                    return (float)$stock . ' ' . $row->unit;
                })
                ->removeColumn('unit')
                ->removeColumn('type')
                ->removeColumn('product_variation')
                ->removeColumn('variation')
                ->make(false);
        }
    }
    
    
    public function getPurchaseSupplier()
    {
        if (request()->ajax()) {
            $start = request()->start_date;
            $end = request()->end_date;
            
            $business_id = request()->session()->get('user.business_id');

            $purchase_supplier = $this->transactionUtil->getSupplierPurchase($business_id, $start, $end);
           
            return Datatables::of($purchase_supplier)
            ->editColumn('transaction_date', function ($row) {
                $tgl1 = $row->transaction_date;
                // $format = request()->session()->get('business.date_format');
                // $tgl = \Carbon::createFromFormat('Y-m-d H:i:s', $tgl1)->format($format);
                $transaction_date = \Carbon::createFromFormat('Y-m-d H:i:s', $tgl1)->format('Y-m-d');
                // $date = \Carbon::now()->subDays($i)->format('Y-m-d');

                return $transaction_date;
            })
            // ->rawColumns(['transaction_date'])
            ->make(true);
        }
    }
    

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchasePaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format("Y-m-d H:i:s");

            $query = Transaction::join(
                            'contacts as c',
                            'transactions.contact_id',
                            '=',
                            'c.id'
                    )
                    ->leftJoin(
                        'transaction_payments as tp',
                        'transactions.id',
                        '=',
                        'tp.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'purchase')
                    ->where('transactions.payment_status', '!=', 'paid');
                    // ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(c.pay_term_type = 'days', c.pay_term_number, 30 * c.pay_term_number) DAY), '$today') <= 7");

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            $dues =  $query->select(
                            'transactions.id as id',
                            'c.name as supplier',
                            'ref_no',
                            'final_total',
                            DB::raw('SUM(tp.amount) as total_paid')
                        )
                        ->groupBy('transactions.id');

            return Datatables::of($dues)
                ->addColumn('due', function ($row) {
                    $total_paid = !empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $row->final_total - $total_paid;
                    return '<span class="display_currency hutang-dua" data-currency_symbol="true" data-orig-value="' . $due . '">' .
                    $due . '</span>';
                })
                ->editColumn('ref_no', function ($row) {
                    if (auth()->user()->can('purchase.view')) {
                        return  '<a href="#" data-href="' . action('PurchaseController@show', [$row->id]) . '"
                                    class="btn-modal" data-container=".view_modal">' . $row->ref_no . '</a>';
                    }
                    return $row->ref_no;
                })
                ->removeColumn('id')
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns([1, 2])
                ->make(false);
        }
    }

    /**
     * Retrieves payment dues for the purchases.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalesPaymentDues()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $today = \Carbon::now()->format("Y-m-d H:i:s");

            $query = Transaction::join(
                            'contacts as c',
                            'transactions.contact_id',
                            '=',
                            'c.id'
                    )
                    ->leftJoin(
                        'transaction_payments as tp',
                        'transactions.id',
                        '=',
                        'tp.transaction_id'
                    )
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.payment_status', '!=', 'paid')
                    ->where('transactions.is_hutang_piutang', '=', '1')
                    // ->where('transactions.jatuh_tempo', '!=', '0')

                    // ->where('transactions.payment_status', '!=', 'paid')
                    // ->whereNotNull('transactions.jatuh_tempo')
                    // ->whereNotNull('transactions.pay_term_number')
                    // ->whereNotNull('transactions.pay_term_type')
                    // ->whereRaw("DATEDIFF( DATE_ADD( transaction_date, INTERVAL IF(transactions.pay_term_type = 'days', transactions.pay_term_number, 30 * transactions.pay_term_number) DAY), '$today') <= 7")
                    ;

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            $dues =  $query->select(
                            'transactions.id as id',
                            'c.name as customer',
                            'transactions.invoice_no',
                            'final_total',
                            // 'transactions.jatuh_tempo',
                            'transactions.transaction_date',
                            'transactions.pay_term_number as no',
                            'transactions.pay_term_type as jenis',
                            DB::raw('SUM(tp.amount) as total_paid')
                        )
                        ->groupBy('transactions.id');
            return Datatables::of($dues)
                
                ->editColumn('invoice_no', function ($row) {
                    if (auth()->user()->can('sell.view')) {
                        return  '<a href="#" data-href="' . action('SellController@show', [$row->id]) . '"
                                    class="btn-modal" data-container=".view_modal">' . $row->invoice_no . '</a>';
                    }
                    return $row->invoice_no;
                })
                ->addColumn('due', function ($row) {
                    // $total_paid = !empty($row->total_paid) ? $row->total_paid : 0;
                    $due = $this->transactionUtil->checkPayment($row->id);
                    // $due = $row->final_total - $total_paid;
                    return '<span class="display_currency hutang-satu" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</span>';
                })
                ->editColumn('transaction_date', function ($row) {
                    // $total_paid = !empty($row->total_paid) ? $row->total_paid : 0;
                    $tgl1 = $row->transaction_date;
                    $nom = $row->no;
                    $format = request()->session()->get('business.date_format');
                    if($row->jenis == 'days'){
                        $tgl = \Carbon::createFromFormat('Y-m-d H:i:s', $tgl1)->addDays($nom)->format($format);
                    }else{
					    $tgl = \Carbon::createFromFormat('Y-m-d H:i:s', $tgl1)->addMonths($nom)->format($format);
                    }
                    
                    return $tgl;
                })
                // ->editColumn('jatuh_tempo', '{{_date($jatuh_tempo)}}')
                ->removeColumn('id')
                ->removeColumn('no')
                ->removeColumn('jenis')
                ->removeColumn('jatuh_tempo')
                // ->orderColumn('tgl', true)
                ->removeColumn('final_total')
                ->removeColumn('total_paid')
                ->rawColumns(['due', 'invoice_no'])
                ->make();
        }
    }
}
