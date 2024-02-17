@extends('layouts.app')
@section('title', 'Pembayaran Hutang')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Pembayaran Hutang
        <small>Kelola Pembayaran Hutang</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">

	<div class="box">
        <div class="box-header">
        	<h3 class="box-title">Kelola Pembayaran Hutang</h3>
            @can('pembayaran_hutang.create')
        	<div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                	data-href="{{action('PembayaranHutangController@create')}}" 
                	data-container=".pembayaran_hutang_modal">
                	<i class="fa fa-plus"></i> Tambah</button>
            </div>
            @endcan
        </div>
        <div class="box-body">
            @can('pembayaran_hutang.view')
            <div class="table-responsive">
        	<table class="table table-bordered table-striped" id="pembayaran_hutang_table">
        		<thead>
        			<tr>
        				<th>Transaction Ref No</th>
        				<th>Payment Ref No</th>
                        <th>Transaction Date</th>
                        <th>Amount</th>
                        <th>@lang( 'messages.action' )</th>
        			</tr>
        		</thead>
        	</table>
            </div>
            @endcan
        </div>
    </div>

    <div class="modal fade pembayaran_hutang_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
    <?php if(session('success')): ?>
        toastr.success('<?php echo session('success'); ?>');
    <?php endif ?>
    var pembayaran_hutang_table = $('#pembayaran_hutang_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{action("PembayaranHutangController@index")}}',
        columnDefs: [ {
            "targets": 2,
            "orderable": false,
            "searchable": false
        } ]
    });
</script>

@endsection
