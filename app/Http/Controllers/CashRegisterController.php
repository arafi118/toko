<?php

namespace App\Http\Controllers;

use App\CashRegister;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\BusinessLocation;
use App\Transaction;
use App\Business;

use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
use App\Utils\CashRegisterUtil;
use Carbon\Carbon;
use CarbonCarbon;
use Illuminate\Support\Facades\Log;

// use DB;

class CashRegisterController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;
    protected $businessUtil;
    protected $cashRegisterUtil;
    protected $payment_types;

    /**
     * Constructor
     *
     * @param CashRegisterUtil $cashRegisterUtil
     * @return void
     */
    public function __construct(
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        CashRegisterUtil $cashRegisterUtil
    ) {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->payment_types = ['cash' => 'Cash', 'card' => 'Card', 'cheque' => 'Cheque', 'bank_transfer' => 'Bank Transfer', 'other' => 'Other'];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('cash_register.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Check if there is a open register, if yes then redirect to POS screen.
        if ($this->cashRegisterUtil->countOpenedRegister() != 0) {
            return redirect()->action('SellPosController@create');
        }

        return view('cash_register.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $initial_amount = 0;
            if (!empty($request->input('amount'))) {
                $initial_amount = $this->cashRegisterUtil->num_uf($request->input('amount'));
            }

            $user_id = $request->session()->get('user.id');
            $business_id = $request->session()->get('user.business_id');

            $register = CashRegister::create([
                'business_id' => $business_id,
                'user_id' => $user_id,
                'status' => 'open'
            ]);
            $register->cash_register_transactions()->create([
                'amount' => $initial_amount,
                'pay_method' => 'cash',
                'type' => 'credit',
                'transaction_type' => 'initial'
            ]);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        }

        return redirect()->action('SellPosController@create');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CashRegister  $cashRegister
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        ini_set('display_errors', '1');
        $register_details =  $this->cashRegisterUtil->getRegisterDetails($id);
        $user_id = $register_details->user_id;
        $open_time = $register_details['open_time'];

        if ($register_details['close_time'] != null) {
            $close_time = $register_details['close_time'];
        } else {
            $close_time = Carbon::now()->toDateTimeString();
        }
        $details = $this->cashRegisterUtil->getRegisterTransactionDetails($user_id, $open_time, $close_time);
        $transaction_sell = $this->cashRegisterUtil->getTransactionSell($user_id, $open_time, $close_time);

        return view('cash_register.register_details')
            ->with(compact('register_details', 'details', 'transaction_sell'));
    }

    /**
     * Shows register details modal.
     *
     * @param  void
     * @return \Illuminate\Http\Response
     */
    public function getRegisterDetails()
    {

        $register_details =  $this->cashRegisterUtil->getRegisterDetails();

        $user_id = auth()->user()->id;
        $open_time = $register_details['open_time'];
        $close_time = Carbon::now()->toDateTimeString();
        $details = $this->cashRegisterUtil->getRegisterTransactionDetails($user_id, $open_time, $close_time);
        $transaction_sell = $this->cashRegisterUtil->getTransactionSell($user_id, $open_time, $close_time);

        return view('cash_register.register_details')
            ->with(compact('register_details', 'details','transaction_sell'));
    }

    /**
     * Shows close register form.
     *
     * @param  void
     * @return \Illuminate\Http\Response
     */
    public function getCloseRegister()
    {
        $register_details =  $this->cashRegisterUtil->getRegisterDetails();

        $user_id = auth()->user()->id;
        $open_time = $register_details['open_time'];
        $close_time = Carbon::now()->toDateTimeString();
        $details = $this->cashRegisterUtil->getRegisterTransactionDetails($user_id, $open_time, $close_time);
        $transaction_sell = $this->cashRegisterUtil->getTransactionSell($user_id, $open_time, $close_time);

        return view('cash_register.close_register_modal')
            ->with(compact('register_details', 'details', 'transaction_sell'));
    }

    /**
     * Closes currently opened register.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postCloseRegister(Request $request)
    {
        try {
            //Disable in demo
            if (config('app.env') == 'demo') {
                $output = [
                    'success' => 0,
                    'msg' => 'Feature disabled in demo!!'
                ];
                return redirect()->action('HomeController@index')->with('status', $output);
            }
            // $get = $request->session()->all();
            // $get = $request->all();
            // dd($get);
            DB::beginTransaction();
            $location_id = $request->only(['location_close']);
            // dd($location_id);
            $input = $request->only([
                'closing_amount', 'total_card_slips', 'total_cheques',
                'closing_note'
            ]);
            $input['closing_amount'] = $this->cashRegisterUtil->num_uf($input['closing_amount']);
            $user_id = $request->session()->get('user.id');
            $business_id = $request->session()->get('user.business_id');
            // $location_id = $request->session()->get('location_id');
            // $business_details = $this->businessUtil->getDetails($business_id);
            // dd($business_details);
            $input['closed_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $input['status'] = 'close';
            $detail_close = $request->all();

            CashRegister::where('user_id', $user_id)
                ->where('status', 'open')
                ->update($input);
            $receipt = $this->receiptContentKasir($business_id, $location_id['location_close'], 'browser', $detail_close);
            // dd($receipt);


            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __('cash_register.close_success'),
                'receipt' => $receipt
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->action('HomeController@index')->with('status', $output);
        // return $output;
    }
    /**
     * Returns the content for the receipt
     *
     * @param  int  $business_id
     * @param  int  $location_id
     * @param string $printer_type = null
     *
     * @return array
     */
    private function receiptContentKasir(
        $business_id,
        $location_id = null,
        $printer_type = null,
        $detail_close
    ) {
        $output = [
            'is_enabled' => false,
            'print_type' => 'browser',
            'html_content' => null,
            'printer_config' => [],
            'data' => []
        ];
        $business_details = $this->businessUtil->getDetails($business_id);
        //   dd($business_id);
        $location_details = BusinessLocation::find($location_id);

        //Check if printing of invoice is enabled or not.

        if ($location_details->print_receipt_on_invoice == 1) {
            //If enabled, get print type.
            $output['is_enabled'] = true;
            // dd($business_id);
            $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $location_id, $location_details->invoice_layout_id);

            //Check if printer setting is provided.
            $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;

            $receipt_details = $this->transactionUtil->getTutupKasir($invoice_layout, $business_details, $location_details, $receipt_printer_type);


            //If print type browser - return the content, printer - return printer config data, and invoice format config
            if ($receipt_printer_type == 'printer') {
                $output['print_type'] = 'printer';
                $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
                $output['data'] = $receipt_details;
            } else {
                $dt_cls = (object)$detail_close;
                // dd($dt_cls);
                // $output['html_content'] = view('sale_pos.receipts.close_pos')->render();
                $output['html_content'] = view('sale_pos.receipts.close_pos', compact('receipt_details', 'dt_cls'))->render();
                // dd($output);

                // $output['html_content'] = $receipt_details;
                // $layout = !empty($receipt_details->design) ? 'sale_pos.receipts.' . $receipt_details->design : 'sale_pos.receipts.classic';

                // $output['html_content'] = view($layout, compact('receipt_details'))->render();
            }
            // $output['html_content'] = view('sell_return.receipt', compact('receipt_details'))->render();
            // $output['html_content'] = $receipt_details;
            // $output = ['success' => 1,
            //                 'msg' => $receipt_details
            //             ];
        }

        return $output;
    }
}
