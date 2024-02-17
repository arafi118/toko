<?php

namespace App\Http\Controllers;

use App\Product;
use App\Variation;
use App\VariationGroupQuantity;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Permission;

class VariationGroupQuantityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $product_id = request()->id;
        $product = Product::findOrFail($product_id);
        $variation = Variation::where('product_id', $product_id)->first();
        $variation_group_quantity = VariationGroupQuantity::where('variation_id', $variation->id)->get();

        // if (request()->ajax()) {
        //                             // ->select(['variation_group_quantities.amount', 'variation_group_quantities.price_inc_tax', 'variation_group_quantities.id']);

        //     return Datatables::of($variation_group_quantity)
        //         ->addColumn(
        //             'action',
        //             '<button data-href="{{action(\'VariationGroupQuantityController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
        //                 &nbsp;
        //                 <button data-href="{{action(\'VariationGroupQuantityController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_spg_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
        //         )
        //         ->removeColumn('id')
        //         ->rawColumns([2])
        //         ->make(false);
        // }

        return view('variation_quantity.index', compact('product','variation','variation_group_quantity'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $variation_id = request()->var_id;

        return view('variation_quantity.create')->with(compact('variation_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input['amount'] = $request->amount;
            $input['price_inc_tax'] = $request->price_inc_tax;
            $input['variation_id'] = $request->variation_id;
            $business_id = $request->session()->get('user.business_id');

            if (VariationGroupQuantity::where('variation_id',$input['variation_id'])->count() >= 2) {
                $output = ['success' => false,
                                'msg' => 'Jumlah paket penjualan sudah maksimal'
                            ];
            } else {
                $spg = VariationGroupQuantity::create($input);
    
                //Create a new permission related to the created price quantity group
                Permission::create(['name' => 'variation_group_quantity.' . $spg->id ]);
    
                $output = ['success' => true,
                                'data' => $spg,
                                'msg' => __("lang_v1.added_success")
                            ];
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VariationGroupQuantity  $variation_quantity
     * @return \Illuminate\Http\Response
     */
    public function show(VariationGroupQuantity $variation_quantity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\VariationGroupQuantity  $variation_quantity
     * @return \Illuminate\Http\Response
     */
    public function edit(VariationGroupQuantity $variation_quantity)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }


        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            return view('variation_quantity.edit')
                ->with(compact('variation_quantity'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VariationGroupQuantity  $variation_quantity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VariationGroupQuantity $variation_quantity)
    {
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input['variation_id'] = $request->variation_id;
                $input['amount'] = $request->amount;
                $input['price_inc_tax'] = $request->price_inc_tax;
                $business_id = $request->session()->get('user.business_id');

                $variation_quantity->variation_id = $input['variation_id'];
                $variation_quantity->amount = $input['amount'];
                $variation_quantity->price_inc_tax = $input['price_inc_tax'];
               
                $variation_quantity->save();

                $output = ['success' => true,
                            'msg' => __("lang_v1.updated_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VariationGroupQuantity  $variation_quantity
     * @return \Illuminate\Http\Response
     */
    public function destroy(VariationGroupQuantity $variation_quantity)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $variation_quantity->delete();

                $output = ['success' => true,
                            'msg' => __("lang_v1.deleted_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }
}