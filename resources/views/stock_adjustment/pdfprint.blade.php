<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stok</title>
</head>
<body>
    <table width="97%" border="0" align="center" cellpadding="3" cellspacing="0">
       <tbody>
          <tr>
              @php
                
                $path = Storage::url('invoice_logos/logo.jpeg');
            @endphp
            <td height="20" colspan="2" class="bottom" width="5%">
                <div class="style9 text-align-left"><img class="" alt="..." src="{{url($path)}}" style="width:65;"></div></td>
                      <td class="bottom">
                 <div class="style9"><h3><b>{{$bl->name}}</b></h3>{{$bl->city}},{{$bl->state}},{{$bl->zip_code}}</div>      
            </td>
            <td height="15" colspan="2" class="bottom"><div align="right" class="style9"></div></td>
          </tr> 
       </tbody>
    </table>
    <hr>
    <br>
        <h3 style="text-align: center;">
           LAPORAN PENYESUAIAN STOK
        </h3>
        <table width="100%" border="1" style="border-collapse: collapse; font-size: 12px;" align="center" cellpadding="3" cellspacing="0" class="style9">
        <thead>
           <tr style="background-color: #ccc">
              
            <th>@lang('messages.date')</th>
            <th>@lang('purchase.ref_no')</th>
            <!--<th>@lang('business.location')</th>-->
            <th>@lang('product.sku')</th>
            <th>@lang('sale.product')</th>
            <th>@lang('report.current_stock')</th>
            <th>@lang('stock_adjustment.hasil_so')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{@format_date($product->transaction_date)}}</td>
                <td>{{$product->ref_no}}</td>
                
                <!--<td>{{$product->location_name}}</td>-->
                <td>{{$product->sub_sku}}</td>
                <td>
                    {{$product->product}}
                    @if( $product->type == 'variable')
                     {{ '-' . $product->product_variation . '-' . $product->variation }} 
                    @endif 
                </td>
                

                <!--@if(!empty($lot_n_exp_enabled))-->
                <!--<td>{{ $product->lot_number or '--' }}-->
                <!--  @if( session()->get('business.enable_product_expiry') == 1 && !empty($product->exp_date))-->
                <!--  ({{@format_date($product->exp_date)}})-->
                <!--  @endif-->
                <!--</td>-->
                <!--@endif-->

                <td>
                    {{@num_format($product->stock)}}
                </td>
                <td>
                    @php
						$kurtam = $product->kurang_tambah == 'kurang' ? "-" : "+";
						echo $kurtam;
					@endphp
                    {{@num_format($product->quantity)}}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>