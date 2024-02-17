<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SimpanBarang;
use App\Brand;
use Yajra\DataTables\Facades\DataTables;

class SimpanBarangController extends Controller
{
    //
    public function index(){
        if (!auth()->user()->can('brand.view') && !auth()->user()->can('brand.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $brands = SimpanBarang::where('business_id', $business_id)
                        ->select(['tempat_simpan', 'keterangan', 'id']);

            return Datatables::of($brands)
                ->addColumn(
                    'action',
                    '@can("brand.update")
                    <button data-href="{{action(\'SimpanBarangController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_rak_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                    @endcan
                    @can("brand.delete")
                        <button data-href="{{action(\'SimpanBarangController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_rak_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->removeColumn('id')
                ->rawColumns([2])
                ->make(false);
        }

        return view('simpan_barang.index');
    }

    public function create(){
        if (!auth()->user()->can('brand.create')) {
            abort(403, 'Unauthorized action.');
        }

        $quick_add = false;
        if(!empty(request()->input('quick_add'))){
            $quick_add = true;
        }

        return view('simpan_barang.create')
                ->with(compact('quick_add'));
    }

    public function store(Request $request){
        if (!auth()->user()->can('brand.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // $input = $request->only(['name', 'keterangan']);
            $simpan_di = $request->input('name');
            $ket = $request->input('keterangan');
            
            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;
            $input['tempat_simpan'] = $simpan_di;
            $input['keterangan'] = $ket;
            $input['created_by'] = $request->session()->get('user.id');

            $rak_bar = SimpanBarang::create($input);
            // dd($rak_bar);
            $output = ['success' => true,
                            'data' => $rak_bar,
                            'msg' => 'Rak Berhasil Ditambahkan'
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    public function edit($id){
        if (!auth()->user()->can('brand.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $rak_bar = SimpanBarang::where('business_id', $business_id)->find($id);

            return view('simpan_barang.edit')
                ->with(compact('rak_bar'));
        }
    }

    public function update(Request $request, $id){
        if (!auth()->user()->can('brand.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'keterangan']);
                $business_id = $request->session()->get('user.business_id');

                $rak_bar = SimpanBarang::where('business_id', $business_id)->findOrFail($id);
                $rak_bar->tempat_simpan = $input['name'];
                $rak_bar->keterangan = $input['keterangan'];
                $rak_bar->save();

                $output = ['success' => true,
                            'msg' => 'Rak Berhasil Diperbarui'
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

    public function destroy($id){
        if (!auth()->user()->can('brand.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $rak_bar = SimpanBarang::where('business_id', $business_id)->findOrFail($id);
                $rak_bar->delete();

                $output = ['success' => true,
                            'msg' => 'Rak Berhasil Dihapus'
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
