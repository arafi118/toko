@if($pos_settings['hide_product_suggestion'] == 0)
@include('sale_pos.partials.product_modal')
@endif

@if(isset($transaction))
@include('sale_pos.partials.edit_discount_modal', ['sales_discount' => $transaction->discount_amount, 'discount_type' =>
$transaction->discount_type])
@else
@include('sale_pos.partials.edit_discount_modal', ['sales_discount' => $business_details->default_sales_discount,
'discount_type' => 'percentage'])
@endif

@if(isset($transaction))
@include('sale_pos.partials.edit_order_tax_modal', ['selected_tax' => $transaction->tax_id])
@else
@include('sale_pos.partials.edit_order_tax_modal', ['selected_tax' => $business_details->default_sales_tax])
@endif

@if(isset($transaction))
@include('sale_pos.partials.edit_shipping_modal', ['shipping_charges' => $transaction->shipping_charges,
'shipping_details' => $transaction->shipping_details])
@else
@include('sale_pos.partials.edit_shipping_modal', ['shipping_charges' => '0.00', 'shipping_details' => ''])
@endif