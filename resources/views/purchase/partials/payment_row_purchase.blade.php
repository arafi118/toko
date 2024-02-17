<div class="row">
	<input type="hidden" class="payment_row_index" value="{{ $row_index}}">
	@php
		$col_class = 'col-md-6';
		if(!empty($accounts)){
			$col_class = 'col-md-4';
		}
	@endphp
	<div class="{{$col_class}}">
		<input type="checkbox" name="is_hutang_piutang" id="is_hutang_piutang"> Hutang
	</div>
	<div class="clearfix"></div>
	<hr>
	<div class="{{$col_class}}">
		<div class="form-group">
			{!! Form::label("amount_$row_index" ,__('sale.amount') . ':*') !!}
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-money"></i>
				</span>
				{!! Form::text("payment[$row_index][amount]", @num_format($payment_line['amount']), ['class' => 'form-control payment-amount input_number', 'required','readonly', 'id' => "amount_$row_index", 'placeholder' => __('sale.amount')]); !!}
			</div>
		</div>
	</div>
	
	<div class="{{$col_class}}">
		<div class="form-group">
			{!! Form::label("method_$row_index" , __('lang_v1.payment_method') . ':*') !!}
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-money"></i>
				</span>
				{!! Form::select("payment[$row_index][method]", $payment_types, $payment_line['method'], ['class' => 'form-control col-md-12 payment_types_dropdown', 'required', 'id' => "method_$row_index", 'style' => 'width:100%;']); !!}
			</div>
		</div>
	</div>
	<div class="{{$col_class}} purchase_bayar_line">
		<div class="form-group">
			{!! Form::label("bayar_purchase" , 'Bayar :*') !!}
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-money"></i>
				</span>
				{!! Form::text("bayar", null, ['class' => 'form-control bayar_purchase input_number','required', 'id' => "bayar_purchase", 'placeholder' => "Nominal", 'autocomplete' => "off"]); !!}
			</div>
		</div>
	</div>
	<style>
		.intro {
		  /* font-size: 150%; */
		  color: red;
		}
	</style>
	<input type="hidden" name="sum_tot_barang" id="sum_tot_barang">
	{{-- <input type="hidden" name="is_hutang_piutang" id="is_hutang_piutang" value="1"> --}}
	@if(!empty($accounts))
		<div class="{{$col_class}}">
			<div class="form-group">
				{!! Form::label("account_$row_index" , __('lang_v1.payment_account') . ':') !!}
				<div class="input-group">
					<span class="input-group-addon">
						<i class="fa fa-money"></i>
					</span>
					{!! Form::select("payment[$row_index][account_id]", $accounts, !empty($payment_line['account_id']) ? $payment_line['account_id'] : '' , ['class' => 'form-control select2', 'id' => "account_$row_index", 'style' => 'width:100%;']); !!}
				</div>
			</div>
		</div>
	@endif
	<div class="clearfix"></div>
		@include('sale_pos.partials.payment_type_details')
	<div class="col-md-12">
		<div class="form-group">
			{!! Form::label("note_$row_index", __('sale.payment_note') . ':') !!}
			{!! Form::textarea("payment[$row_index][note]", $payment_line['note'], ['class' => 'form-control', 'rows' => 3, 'id' => "note_$row_index"]); !!}
		</div>
	</div>
</div>