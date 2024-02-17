@extends('layouts.app')
@section('title', 'Daftar Stock Opname')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Daftar Stok Opname
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
                    {{-- <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('report_sell_period_filter', __('report.date_range') . ':') !!}
                            {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'report_sell_period_filter', 'readonly']); !!}
                        </div>
                    </div> --}}
                    
                    @php
                        $tgl = date('d-m-Y');
                    @endphp
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('tanggal_so', 'Tanggal SO' . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text('tanggal_so', $tgl, ['class' => 'form-control date', 'readonly', 'required']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('rak_id', 'Rak Penyimpanan' . ':') !!}
                            {!! Form::select('rak_id', $rak_bar, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'rak_id']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="button" style="margin-top: 25px" onclick="cetak()" class="btn btn-primary">Cetak</button>
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
                    <table class="table table-bordered table-striped" id="daftar_so">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Produk</th>
                                <th>Rak</th>
                                <th>No Referensi</th>
                                {{-- <th>Harga Beli</th>
                                <th>Stok Sebelum SO</th>
                                <th>Hasil SO</th>
                                <th>Selisih</th>
                                <th>Nilai SO</th> --}}
                            </tr>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
{{-- <div class="modal fade view_register" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div> --}}

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
            window.open(`/penyesuaian/print-daftar-so?tanggal_so=${$('#tanggal_so').val()}&rak_id=${$('#rak_id').val()}`);
        }
    </script>
    <script src="{{ asset('js/stock_adjustment.js?v=' . $asset_v) }}"></script>
@endsection