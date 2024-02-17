<style>
	.flex {
		display: flex;
	}

	.justify-center {
		justify-content: center;
	}

	.justify-between {
		justify-content: space-between;
	}

	.align-items-center {
		align-items: center;
	}

	.flex-column {
		flex-direction: column;
	}

	.flex-row {
		flex-direction: row;
	}

	.flex-wrap {
		flex-wrap: wrap;
	}

	.text-xs {
		font-size: 12pxrem;
	}

	.text-sm {
		font-size: 14px;
	}

	.text-base {
		font-size: 16px;
	}

	.text-lg {
		font-size: 20px;
	}

	.text-xl {
		font-size: 20px;
	}

	.text-2xl {
		font-size: 24px;
	}

	.font-medium {
		font-weight: 500;
	}

	.font-bold {
		font-weight: 700;
	}
</style>
<div class="col-md-12">
	<div class="box box-solid payment_row">
		@if($removable)
		<div class="box-header">
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool remove_payment_row"><i
						class="fa fa-times fa-2x"></i></button>
			</div>
		</div>
		@endif

		@if(!empty($payment_line['id']))
		{!! Form::hidden("payment[$row_index][payment_id]", $payment_line['id']); !!}
		@endif

		<div class="box-body">
			@include('sale_pos.partials.payment_row_form')
		</div>
	</div>
</div>