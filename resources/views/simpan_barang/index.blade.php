@extends('layouts.app')
@section('title', 'Penyimpanan Barang')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Penyimpanan Barang
        <small>Letak barang disimpan</small>
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
        	<h3 class="box-title">Rak Barang</h3>
            @can('brand.create')
            	<div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                    	data-href="{{action('SimpanBarangController@create')}}" 
                    	data-container=".modal_rak_barang">
                    	<i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endcan
        </div>
        <div class="box-body">
            @can('brand.view')
                <div class="table-responsive">
            	<table class="table table-bordered table-striped" id="rak_table">
            		<thead>
            			<tr>
            				<th>Rak</th>
            				<th>Keterangan</th>
            				<th>@lang( 'messages.action' )</th>
            			</tr>
            		</thead>
            	</table>
                </div>
            @endcan
        </div>
    </div>

    <div class="modal fade modal_rak_barang" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
