@if ($print['print_style'] == '2')
@include('labels.partials.styles.2')
@elseif ($print['print_style'] == '3')
@include('labels.partials.styles.3')
@else
@include('labels.partials.styles.1')
@endif
<div id="preview_body">
	@php
	$loop_count = 0;
	@endphp
	@foreach($product_details as $details)
	@php
	$stickers_sheets = $barcode_details->stickers_in_one_sheet;
	$stickers_row = $barcode_details->stickers_in_one_row;
	@endphp
	@while($details['qty'] > 0)
	@php
	$loop_count += 1;
	$is_new_row = (!$barcode_details->is_continuous) && (($loop_count == 1) || ($loop_count %
	$stickers_row) == 1) ? true : false;

	@endphp

	@if(($barcode_details->is_continuous && $loop_count == 1) || (!$barcode_details->is_continuous && ($loop_count %
	$stickers_sheets) == 1))
	{{-- Actual Paper --}}
	<div style="@if(!$barcode_details->is_continuous) height:{{$barcode_details->paper_height}}in !important; @else height:100% !important; @endif width:{{$barcode_details->paper_width}}in !important; line-height: 16px !important;"
		class="label-border-outer">

		{{-- Paper Internal --}}
		<div style="margin-top:{{$barcode_details->top_margin}}in !important; margin-bottom:{{$barcode_details->top_margin}}in !important; margin-left:{{$barcode_details->left_margin}}in !important;margin-right:{{$barcode_details->left_margin}}in !important;"
			class="label-border-internal">
			@endif

			@if((!$barcode_details->is_continuous) && ($loop_count % $stickers_sheets) <= $barcode_details->
				stickers_in_one_row)
				@php $first_row = true; @endphp
				@elseif($barcode_details->is_continuous && ($loop_count <= $stickers_row) ) @php $first_row=true @endphp
					@else @php $first_row=false; @endphp @endif @if ($print['type_card']=='price_card' ) <div
					class="priceTags"
					style="display: inline-block !important; @if(!$is_new_row) margin-left:{{$barcode_details->col_distance}}in !important; @endif @if(!$first_row)margin-top:{{$barcode_details->row_distance}}in !important; @endif">
					<div class="header">
						<div class="text">
							<div class="title">
								{{$details['details']->product_actual_name}}
							</div>
							<div class="barcode">
								<img src="data:image/png;base64,{{DNS1D::getBarcodePNG($details['details']->sub_sku, $details['details']->barcode_type, 2,30,array(39, 48, 54), true)}}"
									alt="" width="60">
								<!--<span> 200 Gr</span>-->
							</div>
						</div>
						<div class="logo"></div>
					</div>
					<div class="body">
						@if(isset($price_quantities[$details['details']->variation_id]))
						<table width="100%">
						    @php
						    $jumlah = 1;
						    @endphp
							@foreach ($price_quantities[$details['details']->variation_id] as $price_quantity)
							@php
							$jumlah++;
							$price = $price_quantity['price']/1000;
							@endphp
							<tr>
								<td>Beli {{ $price_quantity['amount'] }}</td>
								<td>&nbsp;@</td>
								<td>&nbsp;
									<span class="price display_currency" data-currency_symbol=true>
										{{ $price }}
									</span>0,-&nbsp;</td>
								<td>/ {{ $unit->actual_name }}</td>
							</tr>
							@endforeach
							@if ($jumlah <= 1)
							<tr>
								<td>Beli 1 {{ $unit->actual_name }}</td>
							    <td>&nbsp;</td>
							    <td>&nbsp;</td>
							</tr>
							<tr>
								<td colspan="3" style="{!! ($jumlah <= 1) ? 'font-size: 22px; text-align: center;':''; !!}">&nbsp;
									<span class="display_currency" data-currency_symbol=true>
										{{ $details['details']->sell_price_inc_tax/1000 }}
									</span>0,-&nbsp;</td>
							</tr>
							<tr>
							    <td>&nbsp;</td>
							    <td>&nbsp;</td>
							    <td>&nbsp;</td>
							    <td>&nbsp;</td>
							</tr>
							@else
							<tr>
								<td>Beli 1</td>
								<td>&nbsp;@</td>
								<td>&nbsp;
									<span class="price display_currency" data-currency_symbol=true>
										{{ $details['details']->sell_price_inc_tax/1000 }}
									</span>0,-&nbsp;</td>
								<td>/ {{ $unit->actual_name }}</td>
							</tr>
							@endif
						</table>
						@endif
					</div>
					<div class="footer">
						{{$business_name}}
					</div>
		</div>
		@else
		<div style="height:{{$barcode_details->height}}in !important; line-height: {{$barcode_details->height}}in; width:{{$barcode_details->width*0.97}}in !important; display: inline-block; @if(!$is_new_row) margin-left:{{$barcode_details->col_distance}}in !important; @endif @if(!$first_row)margin-top:{{$barcode_details->row_distance}}in !important; @endif"
			class="sticker-border text-center">
			<div style="display:inline-block;vertical-align:middle;line-height:16px !important;">
				{{-- Business Name --}}
				@if(!empty($print['business_name']))
				<b style="display: block !important" class="text-uppercase">{{$business_name}}</b>
				@endif

				{{-- Product Name --}}
				@if(!empty($print['name']))
				<span style="display: block !important">
					{{$details['details']->product_actual_name}}
				</span>
				@endif

				{{-- Variation --}}
				@if(!empty($print['variations']) && $details['details']->is_dummy != 1)
				<span style="display: block !important">
					<b>{{$details['details']->product_variation_name}}</b>:{{$details['details']->variation_name}}
				</span>

				@endif

				{{-- Price --}}
    			@if(!empty($print['price']))
    				<b>Price:</b>
    				<span class="display_currency" data-currency_symbol = true>
    					@if($print['price_type'] == 'inclusive')
    						{{$details['details']->sell_price_inc_tax}}
    					@else
    						{{$details['details']->default_sell_price}}
    					@endif
    				</span>
    			@endif

				{{-- Barcode --}}
				<img class="center-block"
					style="max-width:90% !important;max-height: {{$barcode_details->height/4}}in !important; opacity: 0.9"
					src="data:image/png;base64,{{DNS1D::getBarcodePNG($details['details']->sub_sku, $details['details']->barcode_type, 2,30,array(39, 48, 54), true)}}">


			</div>
		</div>
		@endif

		@if(!$barcode_details->is_continuous && ($loop_count % $barcode_details->stickers_in_one_sheet) ==
		0)
		{{-- Actual Paper --}}
	</div>

	{{-- Paper Internal --}}
</div>
@endif

@php
$details['qty'] = $details['qty'] - 1;
@endphp
@endwhile
@endforeach

@if($barcode_details->is_continuous || ($loop_count % $barcode_details->stickers_in_one_sheet) != 0)
{{-- Actual Paper --}}
</div>

{{-- Paper Internal --}}
</div>
@endif

</div>
<style type="text/css">
	@media print {
		#preview_body {
			display: block !important;
		}
	}

	@page {
		size: {
				{
				$barcode_details->paper_width
			}
		}

		in @if($barcode_details->paper_height !=0) {
				{
				$barcode_details->paper_height
			}
		}

		in @endif;

		/*width: {{$barcode_details->paper_width}}in !important;*/
		/*height:@if($barcode_details->paper_height != 0){{$barcode_details->paper_height}}in !important @else auto @endif;*/
		margin-top: 0in;
		margin-bottom: 0in;
		margin-left: 0in;
		margin-right: 0in;

		@if($barcode_details->is_continuous) age-break-inside : avoid !important;
		@endif
	}
</style>
@if ($print['type_card']=='price_card' )
<script>
	// collect all the divs
	var circle = document.querySelectorAll('.circle');

	// get window width and height
	var winWidth = document.querySelector('.body').offsetWidth;
	var winHeight = document.querySelector('.body').offsetHeight;
	console.log(winWidth, winHeight);

	// i stands for "index". you could also call this banana or haircut. it's a variable
	for (var i = 0; i < circle.length; i++) {

		// shortcut! the current div in the list
		var thisCircle = circle[i];

		// get random numbers for each element
		randomTop = getRandomNumber(0, winHeight);
		randomLeft = getRandomNumber(0, winWidth);

		// update top and left position
		thisCircle.style.top = randomTop + "px";
		thisCircle.style.left = randomLeft + "px";

	}

	// function that returns a random number between a min and max
	function getRandomNumber(min, max) {

		return Math.random() * (max - min) + min;

	}
</script>
@endif