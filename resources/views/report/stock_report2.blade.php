@extends('layouts.app')
@section('title', __('report.stock_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.stock_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary" id="accordion">
              <div class="box-header with-border">
                <h3 class="box-title">
                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
                    <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters')
                  </a>
                </h3>
              </div>
              <div id="collapseFilter" class="panel-collapse active collapse in" aria-expanded="true">
                <div class="box-body">
                  {!! Form::open(['url' => action('ReportController@getStockReport'), 'method' => 'get']) !!}
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                            {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('category_id', __('category.category') . ':') !!}
                            {!! Form::select('category', $categories, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'category_id']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                            {!! Form::select('sub_category', array(), null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'sub_category_id']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('brand', __('product.brand') . ':') !!}
                            {!! Form::select('brand', $brands, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('unit',__('product.unit') . ':') !!}
                            {!! Form::select('unit', $units, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                           <label>Nama Produk:</label>
                           <input type="text" class="form-control" value="{{Request::get('nama_produk') !=null ? Request::get('nama_produk') : ''}}" name="nama_produk" id="nama_produk" placeholder="Nama Produk (Keyword)">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" style="margin-top: 25px;" class="btn btn-primary">Filter</button>
                        <button type="button" style="margin-top: 25px" onclick="cetak()" class="btn btn-warning">Print</button>
                        <button type="button" style="margin-top: 25px"  onclick="pdf()" class="btn btn-danger">Export PDF</button>
                    </div>
                    {!! Form::close() !!}
                </div>
              </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="stock_report_table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>SKU</th>
                                <th>@lang('business.product')</th>
                                <th>@lang('sale.unit_price')</th>
                                <th>@lang('report.current_stock')</th>
                                <th>@lang('report.total_unit_sold')</th>
                                <th>@lang('lang_v1.total_unit_transfered')</th>
                                <th>@lang('lang_v1.total_unit_adjusted')</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            @foreach($products as $p)
                            <tr>
                                <td>{{$p->pid}}</td>
                                <td>{{$p->sku}}</td>
                                <td>{{$p->product}}</td>
                                <td class="text-right">
                                    <?php  
                                        $html = '';
                                        if ($p->type == 'single' && auth()->user()->can('access_default_selling_price')) {
                                            $html .= '<span class="display_currency" data-currency_symbol=true >'
                                            . $p->unit_price . '</span>';
                                        }

                                        if($allowed_selling_price_group){
                                            $html .= ' <button type="button" class="btn btn-primary btn-xs btn-modal" data-container=".view_modal" data-href="' . action('ProductController@viewGroupPrice', [$p->DT_RowId]) .'">' . __('lang_v1.view_group_prices') . '</button>';
                                        }

                                        echo $html;

                                    ?>
                                        
                                </td>
                                <td class="text-center"><?php 
                                    if ($p->enable_stock) {
                                        $stock = $p->stock ? $p->stock : 0 ;
                                        $fstock =  (float)$stock . ' ' . $p->unit;
                                    } else {
                                        $fstock = 'N/A';
                                    }

                                 ?>{{$fstock}}</td>
                                <td class="text-center">
                                    <?php 
                                        $total_sold = 0;
                                        if ($p->total_sold) {
                                            $total_sold =  (float)$p->total_sold;
                                        }

                                        echo '<span class="display_currency" data-currency_symbol=false >' . $total_sold . '</span> ' . $p->unit;
                                     ?>

                                </td>
                                <td class="text-center">
                                    <?php $total_transfered = 0;
                                            if ($p->total_transfered) {
                                                $total_transfered =  (float)$p->total_transfered;
                                            }

                                        echo '<span class="display_currency" data-currency_symbol=false >' . $total_transfered . '</span> ' . $p->unit; ?>
                                </td>
                                <td class="text-center">
                                    <?php  $total_adjusted = 0;
                                            if ($p->total_adjusted) {
                                                $total_adjusted =  (float)$p->total_adjusted;
                                            }

                                            echo '<span class="display_currency" data-currency_symbol=false >' . $total_adjusted . '</span> ' . $p->unit; ?>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                   <center> <p>{{$products->appends(request()->input())->links()}}</p></center>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
    function cetak()
    {
        var x = screen.width/2 - 1000/2;
        var y = screen.height/2 - 500/2;

        window.open('{{url("reports/stock-report")}}?location_id='+$('#location_id').val()+'&category_id='+$('#category_id').val()+'&sub_category_id='+$('#sub_category_id').val()+'&brand='+$('#brand').val()+'&unit='+$('#unit').val()+'&nama_produk='+$('#nama_produk').val()+'&print=ok','Cetak Laporan ',"width=1000, height=500, left="+x+", top="+y+"");
    }

    function pdf()
    {
        location.href = '{{url("reports/stock-report")}}?location_id='+$('#location_id').val()+'&category_id='+$('#category_id').val()+'&sub_category_id='+$('#sub_category_id').val()+'&brand='+$('#brand').val()+'&unit='+$('#unit').val()+'&nama_produk='+$('#nama_produk').val()+'&pdf=ok';
    }
</script>

