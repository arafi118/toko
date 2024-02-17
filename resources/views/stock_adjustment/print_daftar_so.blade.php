<!DOCTYPE html>
<html>
<head>
    @php
        
        $lg_bus = Session::get('business.logo');
	    $logo = !empty($lg_bus) ? $lg_bus : 'logo.jpeg';
    @endphp
    <title>Laporan Stok Opname </title>
</head>
<body>
    
    <table width="97%" border="0" align="center" cellpadding="3" cellspacing="0">
       <tbody>
          <tr>
              @php
                
                $path = Storage::url('business_logos/'. $logo);
            @endphp
            <td height="20" colspan="2" class="bottom" width="5%">
                <div class="style9 text-align-left"><img class="" alt="..." src="{{url($path)}}" style="float:left; width:70px; margin-right:5px;"></div></td>
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
           Laporan Stok Opname <br>  Tanggal {{$tgl}}
        </h3>
        <table width="100%" border="1" style="border-collapse: collapse; font-size: 12px;" align="center" cellpadding="3" cellspacing="0" class="style9">
        <thead>
           <tr style="background-color: #ccc">
              
            <th>No</th>
            <th>Produk</th>
            <th>Rak</th>
            <th>Harga Beli</th>
            <th>Stok Sebelum</th>
            <th>Hasil SO</th>
            <th>Selisih</th>
            <th>Nilai Selisih</th>
            <th>IN</th>
            
            </tr>
        </thead>
        <tbody>
            @foreach($data_so as $data)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{ $data->product_name }}</td>
                <td>{{ $data->rak }}</td>
                @php
                    $val = (float)$data->hpp;
                    $hpp = $val;
                @endphp
                <td>Rp. {{ @num_format($hpp) }}</td>
                <td style="text-align: center;">{{ $data->qty_sblm }} {{$data->satuan}}</td>

                {{-- hasil so --}}
                @php
                    $hasil_so = 0;
                    // $hasil_so = $data->kurtam == "kurang" ? $data->qty_sblm - $data->qty : $data->qty_sblm + $data->qty ;
                    if($data->kurtam == "kurang"){
                        $hasil_so = $data->qty_sblm - $data->qty;
                    }else{
                        $hasil_so = $data->qty_sblm + $data->qty;
                    }
                @endphp
                <td style="text-align: center;">{{ $hasil_so }} {{$data->satuan}}</td>
                {{-- selisih --}}
                <td style="text-align: center;">{{ (float)$data->qty }} {{$data->satuan}}</td>


                {{-- nilai Selisih --}}
                @php
                    $nilai_sel = $data->hpp * $data->qty;
                @endphp
                <td>Rp. {{ @num_format($nilai_sel) }}</td>
                <td>{{ $data->inisial }}</td>
                {{-- <td>{{ @format_date($data->so_date) }}</td> --}}
                {{-- <td>{{ $data->ref_no }}</td> --}}
                {{-- <td>Rp {{@num_format($data->sell_price)}}</td> --}}
                @php
                    // $sold = !empty($data->tot_sold) ? (float)$data->tot_sold : 0;
                @endphp
                {{-- <td>{{ $sold }} {{$data->unit}}</td> --}}
            </tr>
            @endforeach
            
        </tbody>
    </table>
</body>
</html>