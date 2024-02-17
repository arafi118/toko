@extends('layouts.app')
@section('title', __('stock_adjustment.stock_adjustment'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
<br>
    <h1>@lang('stock_adjustment.stock_adjustment')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content no-print">
	{!! Form::open(['url' => action('StockAdjustmentController@getStockAdjustment'), 'method' => 'get' ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">	
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                            {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2']); !!}
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						{!! Form::label('category_id', __('category.category') . ':') !!}
						{!! Form::select('category', $categories, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'category_id']); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('nama_produk', __('purchase.name_product').':') !!}
						<!-- {!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_product_for_srock_adjustment', 'placeholder' => __('stock_adjustment.search_product')]); !!} -->
						<input type="text" class="form-control" value="{{Request::get('nama_produk') !=null ? Request::get('nama_produk') : ''}}" name="nama_produk" id="nama_produk" placeholder="Nama Produk (Keyword)">
					</div>
				</div>
				{{-- <div class="col-md-3">
					<div class="form-group">
						{!! Form::label('brand', __('product.brand') . ':') !!}
						{!! Form::select('brand', $brands, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']); !!}
					</div>
				</div> --}}
				<div class="col-md-3">
					<div class="form-group">
						{!! Form::label('rak_id', 'Rak Penyimpanan' . ':') !!}
						{!! Form::select('rak_id', $rak_bar, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']); !!}
					</div>
				</div>
				<div class="clearfix"></div>
				 {{-- <div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
					</div>
				</div> --}}
				{!! Form::hidden('ref_no', null, ['class' => 'form-control']); !!}
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', @format_date('now'), ['class' => 'form-control date', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('print', __('messages.print').':') !!}
						<div class="box-tools">
							<a class="btn btn-block btn-danger" onclick="cetak()">
							<i class="#"></i>@lang('messages.cetak_form_so')</a>
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('report', __('messages.report').':') !!}
						<div class="box-tools">
							<a class="btn btn-block btn-warning" onclick="pdf()">
							<i class="#"></i>@lang('messages.laporan_so')</a>
						</div>
					</div>	
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('star', 'Cari:') !!}
						<div class="box-tools">
							<button type="submit" class="btn btn-block btn-success">Cari</button>
						</div>
					</div>
				</div>
				<!-- <div class="col-sm-3 pull-right">
					<div class="form-group">
						<select class="form-control select2" name="additional_notes" id="additional_notes">
							<option value="">Pilih</option>
							<option value="Baik">Baik</option>
							<option value="Rusak">Rusak</option>
							<option value="Kelebihan Droping">Kelebihan Droping</option>
						</select>
					</div>
				</div> -->
			</div>
		</div>
	</div> 
	{!! Form::close() !!}
	<!--box end-->
	
<!-- <div class="form-group">
	<select class="form-control select2" name="transaction" id="transaction">
		<option value="">Pilih</option>
			<option value="Hilang">Hilang</option>
			<option value="Rusak">Rusak</option>
			<option value="Kelebihan Droping">Kelebihan Droping</option>
	</select>
</div> -->
<h1></h1>
{!! Form::open(['url' => action('StockAdjustmentController@store'), 'method' => 'post', 'id' => 'stock_adjustment_form' ]) !!}
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-3 pull-right">
					<div class="form-group">
						<button type="submit" class="btn btn-block btn-primary">@lang('messages.save')</button>
						{{-- <div class="input-group"  >
							{!! Form::text('transaction_date', @format_date('now'), ['class' => 'form-control date', 'readonly', 'required']); !!}
						</div> --}}
					</div>
				</div>
				<div class="col-sm-12 col-sm-offset-0">
					{{-- <input type="hidden" id="product_row_index" value="0"> --}}
					<input type="hidden" id="total_amount" name="final_total" value="0">
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-condensed" id="stock_adjustment_product_table">
						<thead>
							<tr>
								<th>ID</th>
								<th class="col-sm-2 text-center">
									@lang('stock_adjustment.sku')
								</th>
								<th class="col-sm-2 text-center">
									@lang('stock_adjustment.product')
								</th>
								<th class="col-sm-2 text-center">
									Stok Saat Ini <br> (Pada Aplikasi)
								</th>
								<th class="col-sm-2 text-center">
									Input SO
								</th>
								<th class="col-sm-2 text-center">
									@lang('stock_adjustment.difference')
								</th>
								<th class="col-sm-2 text-center">
									@lang('stock_adjustment.information')
								</th>
							</tr>
						</thead>
						<tbody>
							<?php $no = 0; ?>
							@foreach($products as $product)
						
						<tr class="product_row">
							<td>{{$product->pid}}</td>
							<td align="center">{{$product->sku}}
								@if( session()->get('business.enable_lot_number') == 1 || session()->get('business.enable_product_expiry') == 1)
								@php
									$lot_enabled = session()->get('business.enable_lot_number');
									$exp_enabled = session()->get('business.enable_product_expiry');
									$lot_no_line_id = '';
									if(!empty($product->lot_no_line_id)){
									$lot_no_line_id = $product->lot_no_line_id;
								}
								@endphp
								@endif
							</td>
							<td align="center">{{$product->product}}</td>
							<td align="center">
								<?php 
								if ($product->stock) {
									$stock = $product->stock ? $product->stock : 0 ;
									$fstock =  (float)$stock . ' ' . $product->unit;
								} else {
									$fstock = 'N/A';
								}
								
								?>{{$fstock}}
							</td>
							@php
								
							@endphp
							<td>
								<!-- {{-- If edit then transaction sell lines will be present --}} -->
								@if(!empty($product->transaction_sell_lines_id))
									<input type="hidden" name="products[{{$no}} ][transaction_sell_lines_id]" class="form-control" value="{{$product->transaction_sell_lines_id}}">
								@endif
	
								<input type="hidden" name="products[{{$no}}][product_id]" class="form-control product_id" value="{{$product->DT_RowId}}">
								{{-- @php
									if($product->unit_price == 0){
										$harga = $product->harga;
									}else {
										$harga = $product->unit_price;
									}
								@endphp --}}
								<input type="hidden" id="" name="products[{{$no}}][harga]" class="form-control harga_ku" value="{{$product->last_purchased_price}}" >

								{{-- <input type="text" id="" name="products[{{$no}}][hbeli]" class="form-control harga_var" value="{{$product->last_purchased_price}}" > --}}
								<input type="hidden" value="{{$product->idv}}" name="products[{{$no}}][variation_id]">
	
								{!! Form::hidden('transaction_date', @format_date('now'), ['class' => 'form-control date', 'readonly', 'required']); !!}
								{!! Form::hidden('location_id', $business_locations, null, ['class' => 'form-control ']); !!}
	
								<input type="hidden" value="{{@num_format($product->stock)}}" name="products[{{$no}}][stock]" required
								class="form-control input-sm input_number stock">
								<input type="hidden" value="0" name="products[{{$no}}][harga_sat]"
								class="form-control input-sm input_number sat_harga">
	
								@if(empty($product->quantity_ordered))
									@php
										$product->quantity_ordered = 0;
									@endphp
								@endif
	
								<input type="text" class="form-control product_quantity input_number" name="products[{{$no}}][quantity]" value="0" 
								@if($product->unit_allow_decimal == 1) data-decimal=1 @else data-rule-abs_digit="true" data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')" data-decimal=0 @endif
								 >
								{{-- <input type="text" class="form-control product_quantity input_number" value="{{@num_format($product->quantity_ordered)}}" name="products[{{$no}}][quantity]" 
								@if($product->unit_allow_decimal == 1) data-decimal=1 @else data-rule-abs_digit="true" data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')" data-decimal=0 @endif
								data-rule-required="true" data-msg-required="@lang('validation.custom-messages.this_field_is_required')" > --}}
								{{$product->unit}}
								
							</td>
							<td>
								<input type="text"  name="products[{{$no}}][unit_price]" class="form-control product_line_total selisih" value="0" id="selisih">
								{{-- <input type="text"  name="products[{{$no}}][jjl]" class="form-control jajal" value="0" id="jajal"> --}}
							</td>
							<td class="form-group">
								<!-- <select class="form-control select2 slct" name="additional_notes" id="additional_notes"> -->
								<select class="form-control select2 slct" name="products[{{$no}}][additional_notes]" id="additional_notes">
									<option value="">Input SO dulu</option>
									{{-- <option value="Baik">Baik</option>
									<option value="Rusak">Rusak</option>
									<option value="Kadaluwarsa">Kadaluwarsa</option> --}}
								</select>
							</td>
						</tr>
						<?php $no++ ;?>
						@endforeach
							</tbody>
						</table>
						<center> <p>{{$products->appends(request()->input())->links()}}</p></center>
					</div>
				</div>
				{{-- <div class="col-sm-4">
					<div class="form-group">
							{!! Form::label('additional_notes', __('stock_adjustment.reason_for_stock_adjustment') . ':') !!}
							{!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'placeholder' => __('stock_adjustment.reason_for_stock_adjustment'), 'rows' => 3]); !!}
					</div>
				</div> --}}
			</div>
			{{-- <div class="row">
				<div class="col-sm-12">
					<button type="submit" class="btn btn-primary pull-right">@lang('messages.save')</button>
				</div>
			</div> --}}

		</div>
	</div>
	
	{!! Form::close() !!}
</section>
@stop
@section('javascript')
	<script src="{{ asset('js/stock_adjustment.js?v=' . $asset_v) }}"></script>
	{{-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> --}}
	<script type="text/javascript">
	
    function cetak()
    {
        var x = screen.width/2 - 1000/2;
        var y = screen.height/2 - 500/2;

        // var dds = [];
        // $('.slct').each(function(index, item) {
        // 	dds.push($(this).val());
        // })

        // var hh = btoa(dds);

        window.open('{{url("/penyesuaian/stock-adjustments")}}?location_id='+$('#location_id').val()+'&category='+$('#category_id').val()+'&additional_notes='+$('#additional_notes').val()+'&nama_produk='+$('#nama_produk').val()+'&rak_id='+$('#rak_id').val()+'&transaction_date='+$('#transaction_date').val()+'&print=ok','_blank');
    }

    function pdf()
    {
    	// var dds = [];
        // $('.slct').each(function(index, item) {
        // 	dds.push($(this).val());
        // })

        // var selisih = [];
        // $('.selisih').each(function() {
        // 	selisih.push($(this).val())
        // })


        // var hh = btoa(dds);
        // var dd = btoa(selisih);

        window.open('{{url("/penyesuaian/stock-adjustments")}}?location_id='+$('#location_id').val()+'&category='+$('#category_id').val()+'&additional_notes='+$('#additional_notes').val()+'&nama_produk='+$('#nama_produk').val()+'&brand='+$('#brand').val()+'&transaction_date='+$('#transaction_date').val()+'&pdf=ok','_blank');
    } 

    // $(document).ready(function(){
	
	// 	$('#selisih').on('change',function(e){
	// 		getKet($(this).val());
	// 	});
	// });

	
	
	

	// $(document).ready(function(){
	// 	$('.auto-save').savy('load');
	// 	$( "#hapus" ).click(function() {
	// 		$('.auto-save').savy('destroy');
	// 	});
	// });

	// $(document).ready(function() {
	// 	$('.date').on('change', function() {
	// 		alert();
	// 	})
	// });
	</script>
@endsection
