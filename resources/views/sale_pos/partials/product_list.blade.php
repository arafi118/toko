<div class="row">
	<div class="col-md-12 eq-height-row">

		@forelse($products as $product)
		<input type="hidden" id="{{$product->variation_id}}" class="product-variant-id" value="0">
		<input type="hidden" class="{{$product->variation_id}}" value="{{$product->product_id}}">
		<div class="col-md-3 col-xs-4 product_list no-print" data-dismiss="modal">
			<div class="product_box bg-gray" data-toggle="tooltip" data-placement="bottom"
				data-variation_id="{{$product->variation_id}}"
				title="{{$product->name}} @if($product->type == 'variable')- {{$product->variation}} @endif {{ '(' . $product->sub_sku . ')'}}">
				<div class="image-container">
					<img src="{{$product->image_url}}" alt="">
				</div>
				<div class="text text-muted text-uppercase">
					<small>{{$product->name}}
						@if($product->type == 'variable')
						- {{$product->variation}}
						@endif
					</small>
				</div>
				<small class="text-muted">
					({{$product->sub_sku}})
				</small>
			</div>
		</div>
		@empty
		<h4 class="text-center">
			@lang('lang_v1.no_products_to_display')
		</h4>
		@endforelse
	</div>
	<div class="col-md-12">
		{{ $products->links('sale_pos.partials.product_list_paginator') }}
	</div>
</div>