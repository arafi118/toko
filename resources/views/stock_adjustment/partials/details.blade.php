<div class="row">
	<div class="col-xs-12 col-sm-10 col-sm-offset-1">
		<div class="table-responsive">
			<table class="table table-condensed bg-gray">
				<tr>
					<th>@lang('product.sku')</th>
					<th>@lang('sale.product')</th>
					@if(!empty($lot_n_exp_enabled))
                		<th>{{ __('lang_v1.lot_n_expiry') }}</th>
              		@endif
              		<th>@lang('report.current_stock')</th>
					<!-- <th>@lang('stock_adjustment.hasil_so')</th>
					<th>@lang('stock_adjustment.difference')</th> -->
					<th>@lang('stock_adjustment.hasil_so')</th>
					<!-- <th>@lang('stock_adjustment.information')</th> -->
					
				</tr>
				<?php 
	                $no = 1;
	                $i = 0;
	             ?>
				@foreach( $stock_adjustment_details as $details )
					<tr>
						<td>
							( {{ $details->sub_sku }} )
						</td>
						<td>
							{{ $details->product }} 
							@if( $details->type == 'variable')
							 {{ '-' . $details->product_variation . '-' . $details->variation }} 
							@endif 
						</td>
						@if(!empty($lot_n_exp_enabled))
                			<td>{{ $details->lot_number or '--' }}
			                  @if( session()->get('business.enable_product_expiry') == 1 && !empty($details->exp_date))
			                    ({{@format_date($details->exp_date)}})
			                  @endif
			                </td>
              			@endif
						<td>
							{{@num_format($details->stock)}}
						</td>
						<td>
						    @php
								$kurtam = $details->kurang_tambah == 'kurang' ? "-" : "+";
								echo $kurtam;
							@endphp
							{{@num_format($details->quantity)}}
						</td>
						<!-- <td>
							{{@num_format($details->stock - $details->quantity)}} 
						</td> -->
						<!-- <td>
							
						</td> -->
					</tr>
					<?php 
		                $no++;
		                $i++;
		            ?>
				@endforeach
			</table>
		</div>
	</div>
</div>