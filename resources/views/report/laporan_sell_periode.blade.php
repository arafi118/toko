@extends('layouts.app')
@section('title', 'Laporan Penjualan Periode')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Laporan Penjualan Periode
    </h1>
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
                  {!! Form::open(['url' => action('ReportController@printSellPeriode'), 'method' => 'get', 'id' => 'report_periode_sell_form' ]) !!}
                    {{-- <div class="col-md-3">
                        <div class="form-group">
                        {!! Form::label('search_product', __('lang_v1.search_product') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-search"></i>
                                </span>
                                <input type="hidden" value="" id="variation_id">
                                {!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'autofocus']); !!}
                            </div>
                        </div>
                    </div> --}}
                    {{-- <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('supplier_id', __('purchase.supplier') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                {!! Form::select('supplier_id', $suppliers, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
                            </div>
                        </div>
                    </div> --}}
                    {{-- <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('location_id', __('purchase.business_location').':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-map-marker"></i>
                                </span>
                                {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('report_sell_period_filter', __('report.date_range') . ':') !!}
                            {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'report_sell_period_filter', 'readonly']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="button" style="margin-top: 25px" onclick="cetak()" class="btn btn-primary">Cetak</button>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::hidden('start_date', \Carbon::createFromTimestamp(strtotime('now'))->format('Y-m-d'), ['class' => 'form-control', 'id' => 'start_date', 'readonly']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::hidden('end_date', \Carbon::createFromTimestamp(strtotime('now'))->format('Y-m-d'), ['class' => 'form-control', 'id' => 'end_date', 'readonly']); !!}
                        </div>
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
                    <table class="table table-bordered table-striped" id="sell_report_period">
                        <thead>
                            <tr>
                                <th>Product ID</th>
                                <th>Produk</th>
                                <th>SKU</th>
                                <th>Harga Jual Satuan</th>
                                <th>Total Terjual</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        {{-- <tfoot>
                            <tr class="bg-gray font-17 footer-total text-center">
                                <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                                <td><span class="display_currency" id="footer_subtotal" data-currency_symbol ="true" style="font-weight: bold;">10000</span></td>
                            </tr>
                        </tfoot> --}}
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade view_register" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

@endsection

{{-- @section('javascript')
<script type="text/javascript">
    function cetak(){
        window.open(`/reports/print-sell-periode?start_date=${$('#start_date').val()}`);
    }
</script>
@endsection --}}

@section('javascript')
    <script type="text/javascript">
        function cetak(){
            window.open(`/reports/print-sell-periode?start_date=${$('#start_date').val()}&end_date=${$('#end_date').val()}`);
        }
    </script>
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
@endsection