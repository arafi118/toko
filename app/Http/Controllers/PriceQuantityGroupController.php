<?php 
namespace App\Http\Controllers;

use App\PriceQuantityGroup;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Permission;

class PriceQuantityGroupController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $price_groups = PriceQuantityGroup::where('price_quantity_groups.business_id', $business_id)
                        ->select(['price_quantity_groups.amount', 'price_quantity_groups.description','price_quantity_groups.id']);

            return Datatables::of($price_groups)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'PriceQuantityGroupController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                        <button data-href="{{action(\'PriceQuantityGroupController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_spg_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
                )
                ->removeColumn('id')
                ->rawColumns([2])
                ->make(false);
        }

        return view('price_quantity_group.index');
    }

    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('price_quantity_group.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input['amount'] = $request->amount;
            $input['description'] = ($request->description) ? $request->description:null;
            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;

            $spg = PriceQuantityGroup::create($input);

            //Create a new permission related to the created price quantity group
            Permission::create(['name' => 'price_quantity_group.' . $spg->id ]);

            $output = ['success' => true,
                            'data' => $spg,
                            'msg' => __("lang_v1.added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    public function edit($id)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }


        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $spg = PriceQuantityGroup::where('business_id', $business_id)->find($id);

            return view('price_quantity_group.edit')
                ->with(compact('spg'));
        }
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input['amount'] = $request->amount;
                $input['description'] = ($request->description) ? $request->description:null;
                $business_id = $request->session()->get('user.business_id');

                $spg = PriceQuantityGroup::where('business_id', $business_id)->findOrFail($id);
                $spg->amount = $input['amount'];
                $spg->description = $input['description'];
               
                $spg->save();

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

    public function destroy($id)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $spg = PriceQuantityGroup::where('business_id', $business_id)->findOrFail($id);
                $spg->delete();

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
?>