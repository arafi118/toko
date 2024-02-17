<!DOCTYPE html>
<html>
<head>
    <title>Form Stok Opname</title>
</head>
<body>
    <table width="97%" border="0" align="center" cellpadding="3" cellspacing="0">
       <tbody>
          <tr>
            <td height="20" colspan="2" class="bottom" width="5%">
                <div class="style9 text-align-left"><img class="" alt="..." src="/images/logo.jpeg" style="float:left; width:90px; margin-right:5px;"></div></td>
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
           FORM STOK OPNAME
        </h3>
        <table style="margin-bottom: 20px">
            <thead>
                <tr>
                    <th width="25%" class="style9">@lang('purchase.business_location'):</th>
                    <th width="25%" class="style9">@lang('purchase.category'):</th>
                    {{-- <th width="40%" class="style9">@lang('purchase.name_product'):</th> --}}
                    <th width="25%" class="style9">Rak Penyimpanan:</th>
                    <th width="25%" class="style9">Tanggal SO:</th>
                </tr>
            </thead>
            <tbody style="text-align: center">
                <tr>
                    <td class="style9">
                        <?php
                        if ($location) {
                            echo "$location->name";
                         } else {
                            echo "Semua";
                         }; ?>
                    </td>
                    <td class="style9">
                        <?php
                        if ($categori) {
                            echo "$categori->name";
                        } else {
                            echo "Semua";
                        }; ?>
                    </td>
                    {{-- <td class="style9">{{ $nama_produk }}</td> --}}
                    <td class="style9">
                        <?php 
                        if ($rak) {
                            echo "$rak->tempat_simpan";
                        } else {
                            echo "Semua";
                        }; ?>
                    </td>
                    <td>{{$transaction_date}}</td>
                </tr>
            </tbody>
        </table>

     <table width="100%" border="1" style="border-collapse: collapse; font-size: 12px;" align="center" cellpadding="3" cellspacing="0" class="style9">
        <thead>
           <tr style="background-color: #ccc">
            <th>No</th>
            <th rowspan="2">ID</th>
            <th rowspan="2">Produk</th>
            <th rowspan="2">SKU</th>
            {{-- <th rowspan="2">Stok Saat Ini <br> (Pada Aplikasi)</th> --}}
            <th>Stok Riil</th>
            
        </tr>
        <?php
            // $dd = base64_decode($_GET['keterangan']);
            // $dd = explode(",", $dd);

         ?>
    </thead>
    <tbody>
        <tr>
            <?php 
                $no = 1;
                $i = 0;
             ?>
            @foreach($products as $product)
            <td align="center">{{$loop->iteration}}</td>
            <td align="center">{{$product->pid}}</td>
            <td>{{$product->product}}</td>
            <td align="center">{{$product->sku}}</td>
            {{-- <td align="center"> --}}
              <?php
            //   if ($product->enable_stock) {
            //     $stock = $product->stock ? $product->stock : 0 ;
            //     $fstock =  (float)$stock . ' ' . $product->unit;
            // } else {
            //     $fstock = 'N/A';
            // }
            ?>
            {{-- </td> --}}
            <td align="center"></td>
           
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