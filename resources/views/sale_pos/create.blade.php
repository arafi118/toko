@extends('layouts.app')

@section('title', 'POS')

@section('content')

<!-- Content Header (Page header) -->
<!-- <section class="content-header">
    <h1>Add Purchase</h1> -->
<!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
<!-- </section> -->
<style>
	.price-box {
		border: 1px solid #ced4da;
		width: 100%;
		vertical-align: middle;
		border-radius: 4px;
	}

	.vertical {
		padding: 16px 8px;
		text-align: center;
	}

	.horizontal {
		padding-left: 16px !important;
		padding-right: 16px !important;
		height: 30px;
		display: flex;
		align-items: center;
	}

	.text-xxl {
		font-size: 32px;
		color: #a80a0a;
	}

	.between {
		justify-content: space-between;
	}
</style>
<input type="hidden" id="__precision" value="{{config('constants.currency_precision')}}">

<!-- Main content -->
<section class="content no-print">
	{!! Form::open(['url' => action('SellPosController@store'), 'method' => 'post', 'id' =>
	'add_pos_sell_form' ]) !!}
	<div class="row">
		<div class="col-md-4">
			<div class="box box-success">
				<div class="box-header">
					<div class="row">
						<div class="col-md-6">
							Pelanggan
						</div>
						<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
						@if(is_null($default_location))
						<div class="col-md-6">
							<div class="form-group" style="margin-bottom: 0px;">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-map-marker"></i>
									</span>

									{!! Form::select('select_location_id', $business_locations, null, ['class' =>
									'form-control input-sm mousetrap',
									'placeholder' => 'Pilih Lokasi',
									'id' => 'select_location_ids', 'autofocus'], $bl_attributes); !!}
									<span class="input-group-addon">
										@show_tooltip(__('tooltip.sale_location'))
									</span>
								</div>
							</div>
						</div>
						@else
        				{!! Form::hidden('select_location_ids', $default_location, ['id' => 'select_location_ids', 'data-receipt_printer_type'
        				=> isset($bl_attributes[$default_location]['data-receipt_printer_type']) ?
        				$bl_attributes[$default_location]['data-receipt_printer_type'] : 'browser']); !!}
						@endif
					</div>
				</div>

				<div class="box-body">
				    {!! Form::hidden('location_id', $default_location, ['id' => 'location_id', 'data-receipt_printer_type'
        				=> isset($bl_attributes[$default_location]['data-receipt_printer_type']) ?
        				$bl_attributes[$default_location]['data-receipt_printer_type'] : 'browser']); !!}
					@if(config('constants.enable_sell_in_diff_currency') == true)
					<div class="col-md-6">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-exchange"></i>
								</span>
								{!! Form::text('exchange_rate', config('constants.currency_exchange_rate'), ['class'
								=> 'form-control input-sm input_number', 'placeholder' =>
								__('lang_v1.currency_exchange_rate'), 'id' => 'exchange_rate']); !!}
							</div>
						</div>
					</div>
					@endif

					@if(!empty($commission_agent))
					<div class="col-md-6">
						<div class="form-group">
							{!! Form::select('commission_agent',
							$commission_agent, null, ['class' => 'form-control select2', 'placeholder' =>
							__('lang_v1.commission_agent')]); !!}
						</div>
					</div>
					@endif

					<input type="hidden" name="hidden_price_group" id="hidden_price_group">
					<input type="hidden" name="hidden_customer_group" id="hidden_customer_group">
					<!-- @if(!empty($price_groups))
							@if(count($price_groups) > 1)
								<div class="col-md-4 col-sm-6">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-money"></i>
											</span>
											@php
												reset($price_groups);
											@endphp
											{!! Form::hidden('hidden_price_group', key($price_groups), ['id' => 'hidden_price_group']) !!}
											{!! Form::select('price_group', $price_groups, null, ['class' => 'form-control select2', 'id' => 'price_group', 'style' => 'width: 100%;']); !!}
											<span class="input-group-addon">
												@show_tooltip(__('lang_v1.price_group_help_text'))
											</span> 
										</div>
									</div>
								</div>
							@else
								@php
									reset($price_groups);
								@endphp
								{!! Form::hidden('price_group', key($price_groups), ['id' => 'price_group']) !!}
							@endif
						@endif -->

					<div class="@if(!empty($commission_agent)) col-sm-12 @else col-sm-12 @endif">
						<div class="form-group" style="width: 100% !important">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-user"></i>
								</span>
								<input type="hidden" id="default_customer_id" value="{{ $walk_in_customer['id']}}">
								<input type="hidden" id="default_customer_name" value="{{ $walk_in_customer['name']}}">
								<input type="hidden" id="default_price_group" value="{{ $walk_in_customer['selling_price_group_id']}}">
								{!! Form::select('contact_id',
								[], null, ['class' => 'form-control mousetrap', 'id' => 'customer_id', 'placeholder'
								=> 'Enter Customer name / phone', 'required', 'style' => 'width: 100%;']); !!}
								<span class="input-group-btn">
									<button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name="" @if(!auth()->user()->can('customer.create')) disabled @endif><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
								</span>
							</div>
						</div>
					</div>
					<input type="hidden" name="pay_term_number" id="pay_term_number" value="{{$walk_in_customer['pay_term_number']}}">
					<input type="hidden" name="pay_term_type" id="pay_term_type" value="{{$walk_in_customer['pay_term_type']}}">

					<div class="@if(!empty($commission_agent)) col-sm-12 @else col-sm-12 @endif">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</span>
								{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' =>
								'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'),
								'disabled' => is_null($default_location)? true : false,
								'autofocus' => is_null($default_location)? false : true,
								]); !!}
								<span class="input-group-btn">
									<button type="button" class="btn btn-default bg-white btn-flat search_product" data-name="">
										<i class="fa fa-cubes text-primary fa-lg"></i>
									</button>
								</span>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>

					<!-- Call restaurant module if defined -->
					@if(in_array('tables' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
					<span id="restaurant_module_span">
						<div class="col-md-3"></div>
					</span>
					@endif
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="box box-success">
				<div class="box-header">
					Detail Transaksi
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-2">
							<b>@lang('sale.item')</b>
							<br />
							<div class="price-box vertical text-xxl">
								<span class="total_quantity">0</span>
							</div>
						</div>
						<div class="@if($pos_settings['disable_order_tax'] != 0) col-md-4 @else col-md-3 @endif">
							<div class="row">
								<div class="@if($pos_settings['disable_discount'] != 0) hide @endif">
									<div class="col-md-12">

										<b>@lang('sale.discount')(-): @show_tooltip(__('tooltip.sale_discount'))</b>
										<br />
										<div class="price-box horizontal between">
											<span id="total_discount">0</span>
											<i class="fa fa-pencil-square-o cursor-pointer" id="pos-edit-discount" title="@lang('sale.edit_discount')" aria-hidden="true" data-toggle="modal" data-target="#posEditDiscountModal"></i>
										</div>
										<input type="hidden" name="discount_type" id="discount_type" value="@if(empty($edit)){{'percentage'}}@else{{$transaction->discount_type}}@endif" data-default="percentage">

										<input type="hidden" name="discount_amount" id="discount_amount" value="@if(empty($edit)) {{@num_format($business_details->default_sales_discount)}} @else {{@num_format($transaction->discount_amount)}} @endif" data-default="{{$business_details->default_sales_discount}}">

									</div>
								</div>
								<div class="col-md-12">
									<div class="@if($pos_settings['disable_discount'] != 0) hide @endif">

										<b>@lang('sale.shipping')(+): @show_tooltip(__('tooltip.shipping'))</b>
										<br />
										<div class="price-box horizontal between">
											<span id="shipping_charges_amount">0</span>
											<i class="fa fa-pencil-square-o cursor-pointer" title="@lang('sale.shipping')" aria-hidden="true" data-toggle="modal" data-target="#posShippingModal"></i>
										</div>
										<input type="hidden" name="shipping_details" id="shipping_details" value="@if(empty($edit)){{""}}@else{{$transaction->shipping_details}}@endif" data-default="">

										<input type="hidden" name="shipping_charges" id="shipping_charges" value="@if(empty($edit)){{@num_format(0.00)}} @else{{@num_format($transaction->shipping_charges)}} @endif" data-default="0.00">

									</div>
								</div>
							</div>
						</div>
						<div class="col-md-3 @if($pos_settings['disable_order_tax'] != 0) hidden @endif">
							<b>@lang('sale.order_tax')(+): @show_tooltip(__('tooltip.sale_tax'))</b>
							<br />
							<div class="price-box horizontal between">
								<span id="order_tax">
									@if(empty($edit))
									0
									@else
									{{$transaction->tax_amount}}
									@endif
								</span>
								<i class="fa fa-pencil-square-o cursor-pointer" title="@lang('sale.edit_order_tax')" aria-hidden="true" data-toggle="modal" data-target="#posEditOrderTaxModal" id="pos-edit-tax"></i>
							</div>

							<input type="hidden" name="tax_rate_id" id="tax_rate_id" value="@if(empty($edit)) {{$business_details->default_sales_tax}} @else {{$transaction->tax_id}} @endif" data-default="{{$business_details->default_sales_tax}}">

							<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" value="@if(empty($edit)) {{@num_format($business_details->tax_calculation_amount)}} @else {{@num_format(optional($transaction->tax)->amount)}} @endif" data-default="{{$business_details->tax_calculation_amount}}">
						</div>
						<div class="@if($pos_settings['disable_order_tax'] != 0) col-md-6 @else col-md-4 @endif">
							<b>@lang('sale.total_payable'):</b>
							<br />
							<input type="hidden" name="final_total" id="final_total_input" value=0>
							<div class="price-box vertical" style="text-align: right;">
								<span id="total_payable" class="text-success lead text-bold" style="font-size: 38px;color: #a80a0a;">0</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

	<div class="row">

		<div class="col-md-8 col-sm-12">
			<div class="box box-success">
				<div class="box-header with-border">
					<div class="col-md-6">
						<h3 class="box-title">POS Terminal <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('sale_pos.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h3>
					</div>
					<div class="col-md-6">
						<b>@lang('sale.total')</b>
						<br />
						<div class="price-box horizontal">
							<span class="price_total">0</span>
						</div>
					</div>
				</div>

				<!-- /.box-header -->
				<div class="box-body">

					<div class="row col-sm-12 pos_product_div">

						<input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{$business_details->sell_price_tax}}">

						<!-- Keeps count of product rows -->
						<input type="hidden" id="product_row_count" value="0">

						@php
						$hide_tax = '';
						if( session()->get('business.enable_inline_tax') == 0){
						$hide_tax = 'hide';
						}
						@endphp
						<table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
							<thead>
								<tr>
									<th class="tex-center col-md-4">
										@lang('sale.product')
										@show_tooltip(__('lang_v1.tooltip_sell_product_column'))
									</th>
									<th class="text-center col-md-3">
										@lang('sale.qty')
									</th>
									<th class="text-center col-md-2">
										@lang('sale.price_inc_tax')
									</th>
									<th class="text-center col-md-3">
										@lang('sale.subtotal')
									</th>
									<th class="text-center"><i class="fa fa-close" aria-hidden="true"></i></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					@include('sale_pos.partials.pos_details')

					{{-- @include('sale_pos.partials.payment_modal') --}}
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
		</div>

		<div class="col-md-4">
			<div class="row">
				<div class="col-md-12">
					<div class="box">
						@include('sale_pos.partials.payment')
					</div>
				</div>
				<div class="col-md-12 hidden">
					<div class="box">
						<div class="box-body">
							<div class="col-sm-2 col-no-padding">

								<button type="button" class="btn btn-warning btn-block btn-flat @if($pos_settings['disable_draft'] != 0) @endif" id="pos-draft" disabled>@lang('sale.draft')</button>

								<button type="button" class="btn btn-info btn-block btn-flat" id="pos-quotation" disabled>@lang('lang_v1.quotation')</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		@if($pos_settings['hide_recent_trans'] == 0)
		@include('sale_pos.partials.recent_transaction_modal')
		@endif
	</div>
	{!! Form::close() !!}
</section>

<!-- This will be printed -->
{{-- <section class="invoice print_section" id="receipt_section">
</section> --}}
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
<!-- /.content -->
<div class="modal fade register_details_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

@stop

@section('javascript')
<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/mask.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
@include('sale_pos.partials.keyboard_shortcuts')

<!-- Call restaurant module if defined -->
@if(in_array('tables' ,$enabled_modules) || in_array('modifiers' ,$enabled_modules) || in_array('service_staff'
,$enabled_modules))
<script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
@endif
@endsection