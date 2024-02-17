<?php
    $lg_bus = Session::get('business.logo');
	$logo = !empty($lg_bus) ? $lg_bus : 'logo.png';
	$thn = Request::get('thn');
	$bln = Request::get('bln');
	$tgl = Request::get('tgl');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <title>COVER</title>
</head>
<style type="text/css">

    .style6 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 16px;  }
    .style9 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 10px; }
    .style10 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 10px; }
    .top	{border-top: 2px solid #000000; }
    .bottom	{border-bottom: 2px solid #000000; }
    .left	{border-left: 1px solid #000000; }
    .right	{border-right: 1px solid #000000; }
    .all	{border: 2px solid #000000; }
    .style26 {font-family: Verdana, Arial, Helvetica, sans-serif}
    .style27 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; }
    .align-justify {text-align:justify; }
    .align-center {text-align:center; }
    .align-right {text-align:right; }
    
    </style>
<body>
    <?php $bulan=array("01"=>"Januari",
    					   "02"=>"Februari",
    					   "03"=>"Maret",
    					   "04"=>"April",
    					   "05"=>"Mei",
    					   "06"=>"Juni",
    					   "07"=>"Juli",
    					   "08"=>"Agustus",
    					   "09"=>"September",
    					   "10"=>"Oktober",
    					   "11"=>"November",
    					   "12"=>"Desember");?>

@php
$path = Storage::url('business_logos/'. $logo);
@endphp
    <table width="97%" border="0" align="center" cellpadding="3" cellspacing="0" class="all">
		<tbody>
			<tr align="center" height="80">
                  <td colspan="5" class="style6">
                    &nbsp;
                  </td>
            </tr>
              <tr align="center">
                  <td colspan="5" class="style6"> 
                    <strong>LAPORAN 
                    @php
                        if($tgl!=null){
                          echo "HARIAN";
                        }elseif($bln!=null){
                          echo "BULANAN";
                        }elseif($thn!=null){
                          echo "TAHUNAN";
                        }
                    @endphp
                    </strong> 
                    <br> {{Request::get('tgl')!=null ? Request::get('tgl') : ''}}
                     {{Request::get('bln')!=null ? strtoupper(array_search(Request::get('bln'), array_flip($bulan)))  : ''}} {{Request::get('thn')!=null ? Request::get('thn') : ''}}
                  </td>
              </tr>
              <tr align="center" height="80">
                <td colspan="5" class="style6">
                  &nbsp;
                </td>
            </tr>
              <tr height="40" style="">
                  <td colspan="5">
                      <center>
                          <img src="{{url($path)}}" alt="" style="width: 20%;">
                          {{-- <img src="../../../images/logo.jpeg" alt="" style="width: 20%;"> --}}
                      </center>
                  </td>
              </tr>
              <tr align="center" height="80">
                <td colspan="5" class="style6">
                  &nbsp;
                </td>
            </tr>
              <tr align="center" height="230">
                  <td class="style6" colspan="5">
                      <center>
                        <img src="../../../images/i.png" alt="" style="width: 5%;">
                      </center>
                  </td>
              </tr>
              <tr align="center" height="80">
                <td colspan="5" class="style6">
                  &nbsp;
                </td>
            </tr>
              <tr align="center">
                  <td colspan="5" class="style6">
                    
                    <b>{{$bl->name}} <br>{{$bl->city}},{{$bl->state}}</b><br>
                    <span class="style9">{{$bl->landmark}}, ,{{$bl->zip_code}}, ,{{$bl->mobile}}
                    </span>
                  </td>
              </tr>
		</tbody>
	</table>
</body>
</html>