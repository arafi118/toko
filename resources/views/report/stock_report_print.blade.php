<!DOCTYPE html>
<html>
<head>
	<title>Laporan Stok</title>
</head>
<body>
	<table width="97%" border="0" align="center" cellpadding="3" cellspacing="0">
       <tbody>
       	  <tr>
			<td height="20" colspan="2" class="bottom" width="5%">
				<div class="style9 text-align-left"><img class="" alt="..." src="../../../images/logo/1.png" style="float:left; width:50; margin-right:5px;"></div></td>
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
                <th>ID</th>
                <th>SKU</th>
                <th>@lang('business.product')</th>
                <th>@lang('sale.unit_price')</th>
                <th>@lang('report.current_stock')</th>
                <th>@lang('report.total_unit_sold')</th>
                <th>@lang('lang_v1.total_unit_transfered')</th>
                <th>@lang('lang_v1.total_unit_adjusted')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$p->pid}}</td>
                <td>{{$p->sku}}</td>
                <td>{{$p->product}}</td>
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
                    if ($p->enable_stock) {
                        $stock = $p->stock ? $p->stock : 0 ;
                        $fstock =  (float)$stock . ' ' . $p->unit;
                    } else {
                        $fstock = 'N/A';
                    }

                 ?>{{$fstock}}
                </td>
                <td align="center">
                    <?php 
                        $total_sold = 0;
                        if ($p->total_sold) {
                            $total_sold =  (float)$p->total_sold;
                        }

                        echo '<span class="display_currency" data-currency_symbol=false >' . $total_sold . '</span> ' . $p->unit;
                     ?>

                </td>
                <td align="center">
                   <?php $total_transfered = 0;
                            if ($p->total_transfered) {
                                $total_transfered =  (float)$p->total_transfered;
                            }

                        echo '<span class="display_currency" data-currency_symbol=false >' . $total_transfered . '</span> ' . $p->unit; ?>
                </td>
                <td align="center">
                     <?php  $total_adjusted = 0;
                            if ($p->total_adjusted) {
                                $total_adjusted =  (float)$p->total_adjusted;
                            }

                            echo '<span class="display_currency" data-currency_symbol=false >' . $total_adjusted . '</span> ' . $p->unit; ?>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
