@extends('layouts.app')
@section('title', 'Tutup Saldo')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Tutup Saldo Bulanan
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
                     <label>Pilih Rekening</label>
                     <select class="form-control select2" name="jenis_neraca" id="jenis_neraca">
                       @foreach ($rill as $rl)
                       <option value="{{$rl->idrekening}}">{{$rl->nama_rr}}</option>
                       @endforeach
                     </select>
                   </div>
                </div>
               <div class="col-md-6" >
                  <a class="btn btn-app" id="preview" href="javascript:void(0)" onclick="tutupSaldo()">
                    <i class="fa fa-book"></i> Tutup Saldo
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
  function tutupSaldo() {
      var idrekening = $('#jenis_neraca').val()
      var tgl        = $('#harian').val();
      var bln        = $('#bulanan').val();
      var thn        = $('#tahunan').val();
      
      $.ajax({
          type: 'GET',
          url : '/laporan/saldo_neraca',
          data: {
              'id_rekening':idrekening,
              'tgl':tgl,
              'bln':bln,
              'thn':thn
          },
          success: function(result) {
                if (result.status) {
                    swal('Berhasil',result.msg,'success');
                    return;
                }
                
                swal('Coba Lagi','Tutup Saldo Bulanan Gagal','warning');
                return;
          },
          error: function() {
                swal('Coba Lagi','Tutup Saldo Bulanan Gagal','warning');
                return;
          }
      })
  }
  
</script>
@endsection