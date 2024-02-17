@extends('layouts.app')
@section('title', 'Laporan')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Laporan
        <small>Halaman Pelaporan</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">

	<div class="box">
       <!--  <div class="box-header">
        	<h3 class="box-title">Kelola Jurnal Umum</h3>
        </div> -->
        <div class="box-body">
           <div class="row">
               <div class="col-md-4">
                   <div class="form-group">
                       <label>Tahunan</label>
                       <select class="form-control select2" name="tahunan" id="tahunan">
                           <?php for($i=date('Y'); $i>=date('Y')-32; $i-=1):?>
                           <option value="{{$i}}" <?php if($i == date('Y')): ?> selected="" <?php endif ?>>{{$i}}</option>
                           <?php endfor ?>
                       </select>
                   </div>
               </div>
               <div class="col-md-4">
                   <div class="form-group">
                       <label>Bulanan</label>
                       <select class="form-control select2" name="bulanan" id="bulanan">
                           <?php $bulan=array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember");?>
                           @foreach($bulan as $ky=>$vl)
                           <option value="{{$ky}}" <?php if($ky == date('m')): ?> selected="" <?php endif ?> >{{$vl}}</option>
                           @endforeach
                       </select>
                   </div>
               </div>
               <div class="col-md-4">
                   <div class="form-group">
                       <label>Harian</label>
                       <select class="form-control select2" name="harian" id="harian">
                           <option value="">- sepanjang bulan</option>
                           <?php for($d=1; $d<32; $d++): ?>
                           <option value="{{$d}}">{{$d}}</option>
                           <?php endfor ?>
                       </select>
                   </div>
               </div>
           </div>
           <hr>
           <div class="row">
                <div class="col-md-6">
                   <div class="form-group">
                       <label>Nama Laporans</label>
                       <select class="form-control select2" name="namalaporan" id="namalaporan">
                           <option value="">- pilih nama laporan</option>
                           <option value="cover">COVER</option>
                           <option value="neraca">Neraca</option>
                           <option value="laba_rugi">Laba Rugi</option>
                           <option value="inventaris">Inventaris</option>
                           <option value="buku_besar">Buku Besar</option>
                           <option data-id="1" value="stock-report">Stok</option>
                           <option data-id="1" value="purchase-sell">Pembelian & Penjualan</option>
                           <option data-id="1" value="tax-report">Pajak</option>
                           <option data-id="1" value="customer-supplier">Pemasok & Pelanggan</option>
                           <option data-id="1" value="customer-group">Kelompok Penjualan</option>
                           <option data-id="1" value="lot-report">Lot</option>
                           <option data-id="1" value="trending-products">Produk Terpopuler</option>
                           <option data-id="1" value="stock-adjustment-report">Penyesuaian Stok</option>
                           <option data-id="1" value="product-purchase-report">Pembelian Produk</option>
                           <option data-id="1" value="product-sell-report">Penjualan Produk</option>
                           <option data-id="1" value="purchase-payment-report">Pembayaran Pembelian</option>
                           <option data-id="1" value="sell-payment-report">Pembayaran Penjualan</option>
                           <option data-id="1" value="expense-report">Pengeluaran</option>
                           <option data-id="1" value="register-report">Kasir</option>
                           <option data-id="1" value="sales-representative-report">Staf Penjualan</option>
                       </select>
                   </div>
                   <div class="form-group bukubesardiv">
                     <label>Jenis Buku Besar</label>
                     <select class="form-control select2" name="jenis_buku_besar" id="jenis_buku_besar">
                       @foreach($jenisbuku as $jb)
                        <option value="{{strtolower($jb->ins)}}">{{$jb->nama_jb}}</option>
                       @endforeach
                     </select>
                   </div>
                   <div class="form-group inventarisdiv">
                     <label>Jenis Inventaris</label>
                     <select class="form-control select2" name="jenis_inventaris" id="jenis_inventaris">
                       <option value="inventaris">Inventaris</option>
                       <option value="biaya_dibayar_dimuka">Biaya Dibayar Dimuka</option>
                       <option value="aktiva_tetap">Aktiva Tetap</option>
                     </select>
                   </div>
                   <div class="form-group neracadiv">
                     <label>Pilih Rekening</label>
                     <select class="form-control select2" name="jenis_neraca" id="jenis_neraca">
                       <option selected value="jenis_rekening=all">All</option>
                       @foreach ($rill as $rl)
                       <option value="jenis_rekening={{$rl->idrekening}}">{{$rl->nama_rr}}</option>
                       @endforeach
                     </select>
                   </div>
                </div>
               <div class="col-md-6" >
                  <label>Pilih format laporan</label>  <br>
                  <a class="btn btn-app" id="preview" href="javascript:void(0)" onclick="cetak('preview')">
                    <i class="fa fa-globe"></i> Preview
                  </a>
                  <!-- <a class="btn btn-app" id="pdf" href="javascript:void(0)" onclick="cetak('pdf')">
                    <i class="fa fa-file-pdf-o"></i> PDF
                  </a>
                  <a class="btn btn-app" id="tes" href="javascript:void(0)" onclick="openstream()"  target="_blank">
                    <i class="fa fa-globe"></i> Preview
                  </a> -->
                  <a class="btn btn-app" id="show" href="javascript:void(0)" onclick="cetak('preview')">
                    <i class="fa fa-file"></i> Show 
                  </a>
               </div>
           </div>
        </div>
    </div>
</section>

<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
  $(function(){
    $('.bukubesardiv').hide();
    $('.inventarisdiv').hide();
    $('.neracadiv').hide();
    $('#show').hide();
    $('#namalaporan').on('change',function(){
        var type_laporan = $(this).children('option:selected').data('id');

        if(type_laporan == 1){
          $('#show').show();
          $('#preview').hide();
          $('#pdf').hide();
        }else{
          $('#show').hide();
          $('#preview').show();
          $('#pdf').show();
        }

        if($('#namalaporan').val() == 'buku_besar'){
          $('.bukubesardiv').show();
        }else{
          $('.bukubesardiv').hide();
        }
        
        if($('#namalaporan').val() == 'inventaris'){
          $('.inventarisdiv').show();
        }else{
          $('.inventarisdiv').hide();
        }
        
        if($('#namalaporan').val() == 'neraca'){
          $('.neracadiv').show();
        }else{
          $('.neracadiv').hide();
        }
        
    });
  });
  function cetak(type){
    var namalaporan    = $('#namalaporan').val();
    var jenisbukubesar = $('#jenis_buku_besar').val();
    var jenis_inventaris = $('#jenis_inventaris').val();
    var neracadiv = $('#jenis_neraca').val()
    if($('#namalaporan').children('option:selected').data('id') == 1)
    {
      var urlx = "{{url('reports')}}/"+namalaporan;

      window.open(urlx,'_blank');
    }
    else{
      var tgl         = $('#harian').val();
      var bln         = $('#bulanan').val();
      var thn         = $('#tahunan').val();
      var urlx ='';
      if(namalaporan == 'buku_besar'){
        urlx        = "{{url('laporan')}}/"+namalaporan+'?jenis_buku_besar='+jenisbukubesar+'&type='+type+'&tgl='+tgl+'&bln='+bln+'&thn='+thn;
        window.open(urlx,'_blank');
      } else if(namalaporan == 'inventaris') {
        urlx        = "{{url('laporan')}}/"+jenis_inventaris+'?type='+type+'&tgl='+tgl+'&bln='+bln+'&thn='+thn;
        window.open(urlx,'_blank');
      } else if(namalaporan == 'neraca') {
        urlx        = "{{url('laporan')}}/"+namalaporan+'?type='+type+'&tgl='+tgl+'&bln='+bln+'&thn='+thn+'&'+neracadiv;
        window.open(urlx,'_blank');
      } else{
        urlx        = "{{url('laporan')}}/"+namalaporan+'?type='+type+'&tgl='+tgl+'&bln='+bln+'&thn='+thn;
        window.open(urlx,'_blank');
      }
      
      var x           = screen.width/2 - 1000/2;
      var y           = screen.height/2 - 500/2;
      
      if(namalaporan == ''){
        swal('Warning','Nama laporan harus diisi','error');

        return;
      }

      if(thn == ''){
        swal('Warning','Tahun harus diisi','error');

        return;
      }

        window.open(urlx,'Cetak Laporan',"width=1000, height=500, left="+x+", top="+y+"");
      }
  }
  
</script>
@endsection