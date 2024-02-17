@extends('layouts.app')
@section('title', 'Tutup Buku')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Laporan
        <small>Tutup Buku</small>
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
                   <form method="post" action="{{url('do_tutup_buku')}}" onsubmit="return confirm('Anda ingin memproses data?')">
                   {{csrf_field()}}
                   <div class="form-group">
                       <label>Tahunan</label>
                       <select class="form-control select2" name="tahunan" id="tahunan" required="">
                           <option value="">- pilih tahun</option>
                           <?php for($i=date('Y'); $i>=date('Y')-32; $i-=1):?>
                           <option value="{{$i}}">{{$i}}</option>
                           <?php endfor ?>
                       </select>
                   </div>
                   <button type="submit" class="btn btn-primary">Tutup Buku</button>
                  </form>
               </div>
           </div>
           <div class="row">
             <div class="col-md-6">
               <h3>Aktiva</h3>
               @foreach($rekening_aktiva as $ra)
               <h4>{{$ra->nama_rr}}</h4>
               @endforeach
             </div>
              <div class="col-md-6">
               <h3>Passiva</h3>
               @foreach($rekening_passiva as $rp)
               <h4>{{$rp->nama_rr}}</h4>
               @endforeach
             </div>
           </div>
           
           </div>
           <hr>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">  
<?php if(session('success')): ?>
    toastr.success('<?php echo session('success'); ?>');
<?php endif ?>
</script>
@endsection

