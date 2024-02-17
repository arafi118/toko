<!DOCTYPE html>
<html>
<head>
    @php
        $sd = !empty($start_date) && $start_date == $end_date ? "Harian" : \Carbon::createFromFormat('Y-m-d', $start_date)->format('d M Y');
        $ed = \Carbon::createFromFormat('Y-m-d', $end_date)->format('d M Y');
        $date = "$sd - $ed";
    @endphp
    <title>Laporan Penjualan Periode {{$date}}</title>
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
                 <div class="style9"><h3><b>{{$bl->name}}</b></h3>{{$bl->city}}, {{$bl->state}}, {{$bl->zip_code}}</div>      
            </td>
            <td height="15" colspan="2" class="bottom"><div align="right" class="style9"></div></td>
          </tr> 
       </tbody>
       
    </table>
    <hr>
    <br>
        <h3 style="text-align: center;">
           Laporan Penjualan Periode <br> {{$date}}
        </h3>
        <table width="100%" border="1" style="border-collapse: collapse; font-size: 12px;" align="center" cellpadding="3" cellspacing="0" class="style9">
        <thead>
           <tr style="background-color: #ccc">
              
            <th>ID Produk</th>
            <th>Produk</th>
            <th>SKU</th>
            <th>Harga Jual Satuan</th>
            <th>Total Terjual</th>
            
            </tr>
        </thead>
        <tbody>
            @foreach($sell_data as $data)
            <tr>
                <td>{{ $data->product_id }}</td>
                <td>{{ $data->product_name }}</td>
                <td>{{ $data->sku }}</td>
                <td>Rp {{@num_format($data->sell_price)}}</td>
                @php
                    $sold = !empty($data->tot_sold) ? (float)$data->tot_sold : 0;
                @endphp
                <td>{{ $sold }} {{$data->unit}}</td>
            </tr>
            @endforeach
            
        </tbody>
    </table>
</body>
</html>