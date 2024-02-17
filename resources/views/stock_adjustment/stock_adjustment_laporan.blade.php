``  <!DOCTYPE html>
<html>
<head>
  <title>Laporan Stok</title>
</head>
<body>
  <table width="97%" border="0" align="center" cellpadding="3" cellspacing="0">
       <tbody>
          <tr>
            <td height="20" colspan="2" class="bottom" width="5%">
                <div class="style9 text-align-left"><img class="" alt="..." src="/images/logo.jpeg" style="float:left; width:50; margin-right:5px;"></div></td>
                    <td class="bottom">
                        <div class="style9"><h3><b>{{$bl->name}}</b></h3>{{$bl->city}},{{$bl->state}},{{$bl->zip_code}}</div>      
                    </td>
            <td height="20" colspan="2" class="bottom"><div align="right" class="style9"></div></td>
        </tr> 
     </tbody>
  </table>
  <hr>
    <br>
        <h3 style="text-align: center;">
           LAPORAN STOK
        </h3>
    <table width="100%" border="1" style="border-collapse: collapse; font-size: 12px;" align="center" cellpadding="3" cellspacing="0" class="style9">
        <thead>
           <tr style="background-color: #ccc">
              
                <th>No</th>
                <th>@lang('business.product')</th>
                <th>SKU</th>
                <th>@lang('sale.unit_price')</th>
                <th>Stok Dalam Asta POS</th>
                <th>Selisih Stok</th>
                <th>Nilai Selisih Persediaan</th>
                <th>Keterangan</th>
            </tr>
            <?php
              // $dd = base64_decode($_GET['keterangan']);
              // $dd = explode(",", $dd);
              // $selisih = base64_decode($_GET['selisih']);
              // $selisih = explode(",", $selisih);
            ?> 
        </thead>
        <tbody>
          <?php 
                $no = 1;
                $i = 0;
             ?>
            @foreach($products as $p)
            <tr>
              <td align="center">{{$no}}</td>
              <td>{{$p->product}}</td>
              <td align="center">{{$p->sku}}</td>
              <td align="right">
                  <?php  
                        $html = '';
                            $html .= '<span class="display_currency" data-currency_symbol=true >'
                            . $p->unit_price . '</span>';
                        echo $html;
                    ?>
                </td>
                  <td align="center">
                <?php 
                if ($p->stock) {
                  $stock = $p->stock ? $p->stock : 0 ;
                  $fstock =  (float)$stock . ' ' . $p->unit;
                } else {
                  $fstock = 'N/A';
                }

                ?>{{$fstock}}
                </td>
                <td align="center">
                  {{-- {{$selisih[$i] - $p->quantity}} --}}
                </td> 
                <td align="center">
                  {{-- {{$stock - $selisih[$i]}} --}}
                </td>
                <td align="center">
                 
                </td>
            </tr>
            <?php 
                $no++;
                $i++;
            ?>
            @endforeach
        </tbody>
    </table>
</body>
</html>
