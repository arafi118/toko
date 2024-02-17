@extends('layouts.app')
@section('title', 'Jurnal Umum')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Jurnal Umum
        <small>Kelola Jurnal Umum</small>
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
        	<h3 class="box-title">Kelola Jurnal Umum</h3>
            @can('jurnal.create')
        	<div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                	data-href="{{action('JurnalController@create')}}" 
                	data-container=".jurnal_modal">
                	<i class="fa fa-plus"></i> Tambah</button>
            </div>
            @endcan
        </div>
        <div class="box-body">
            @can('jurnal.view')
            <div class="table-responsive">
        	<table class="table table-bordered table-striped" id="jurnal_table">
        		<thead>
        			<tr>
        				<th>Jenis Buku</th>
        				<th>Rekening Debit</th>
                        <th>Rekening Kredit</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Nominal</th>
                        <th>@lang( 'messages.action' )</th>
        			</tr>
        		</thead>
        	</table>
            </div>
           @endcan
        </div>
    </div>

    <div class="modal fade jurnal_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
    
    $(document).ready(function(){
        
        
        var jurnal_table = $('#jurnal_table').DataTable({
					processing: true,
					serverSide: true,
					ajax: '/jurnal',
					columnDefs: [ {
						"targets": 2,
						"orderable": false,
						"searchable": false
					} ]
			    });
    });
    
    $(function(){
        
        
        $('.jurnal_modal').on('shown.bs.modal', function (e) {
            $('.select2').select2();
            $('#bukan_inven').show()
            $('#ini_inven').hide()
                    
            
            $('.jurnal_modal .hutangdiv').hide();
            if($('#jenisbuku').val() == '211' || $('#jenisbuku').val() == '212' || $('#jenisbuku').val() == '213'){
                $('.jurnal_modal .hutangdiv').show();
            }else{
                $('.jurnal_modal .hutangdiv').hide();
            }
            
            
            $('.jurnal_modal #jenismutasi').on('change',function(e){
                getRekening($('.jurnal_modal #jenisbuku').val(),null,$(this).val());
            });
            
            
            $('.jurnal_modal #tanggaljurnal').datepicker({autoclose:true});
            $('.jurnal_modal #jenisbuku').on('change',function(e){
               getRekening($(this).val(),null,$('.jurnal_modal #jenismutasi').val());
            });
            $('.jurnal_modal #namarekening').on('change',function(e){
               getPasangan($(this).find(":selected").data("pasangan"));
            });
            
            $('.jurnal_modal #refhutang').on('change',function(e){
               getHutang($(this).val());
            });
            
            $('.jurnal_modal #nominal').on('keyup',function(e){
               perolehan();
            });
            
            $('.jurnal_modal #jumlah').on('keyup',function(e){
               perolehan();
            });
            
            $(".custom").on('keyup',function(){
                $(".inventory").val($(this).val());
            })
            $(".inventory").on('keyup',function(){
                $(".custom").val($(this).val());
            })
        }); 
    });
    // var jurnal_table = $('#jurnal_table').DataTable({
    //                 processing: true,
    //                 serverSide: true,
    //                 ajax: '{{action("JurnalController@index")}}',
    //                 columnDefs: [ {
    //                     "targets": 2,
    //                     "orderable": false,
    //                     "searchable": false
    //                 } ]
    //             });
    $(document).on('click', 'button.edit_jurnal_button', function(){

        $( "div.jurnal_modal" ).load( $(this).data('href'), function(){

            $(this).modal('show');
           
            getRekening($('#jurnal_edit_form #jenisbuku').val(),$('#jurnal_edit_form #kd_rekening').val());
            
            getPasangan($('#jurnal_edit_form #kd_rekening_pasangan').val());

            $('form#jurnal_edit_form').submit(function(e){
                e.preventDefault();
                var data = $(this).serialize();

                $.ajax({
                    method: "POST",
                    url: $(this).attr("action"),
                    dataType: "json",
                    data: data,
                    success: function(result){
                        if(result.success === true){
                            $('div.jurnal_modal').modal('hide');
                            toastr.success(result.msg);
                            jurnal_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });
        });
    });
    
    function perolehan() {
        var nominal = $('#nominal').val() || 0
        var jumlah = $('#jumlah').val() || 0
        
        var i = 0;
        while (i < nominal.length) {
            nominal = nominal.toString().replace('.','')
            i++
        }
        
        var perolehan = parseInt(nominal) * parseInt(jumlah)
        $('#perolehan').val(perolehan)
    }

    function getRekening(id,kd_rekening=null,jenis_trx){
         if($('#jenisbuku').val() == '211' || $('#jenisbuku').val() == '212' || $('#jenisbuku').val() == '213'){
                $('.jurnal_modal .hutangdiv').show();
            }else{
                $('.jurnal_modal .hutangdiv').hide();
            }
        if(id !=null && id !=''){
            
            $.ajax({
                type:'GET',
                url : '{{route("jurnal.get_rekening",["id"=>null, "jenis_trx"=>null])}}/'+id +'/'+jenis_trx,
                data:null,
                dataType:'json',
                success:function(i){
    
                    var html = '';
                        html += '<option value="">- pilih rekening terlebih dahulu</option>';
                    $.each(i.rekening,function(k,v){
                        if(kd_rekening !=null){
                            if(kd_rekening == v.kd_rekening){
    
                                html += '<option value="'+v.kd_rekening+'" selected="" data-pasangan="'+v.pasangan+'">'+v.kd_rekening+' - '+v.nama_rekening+'</option>';
                            }else{
    
                                html += '<option value="'+v.kd_rekening+'" data-pasangan="'+v.pasangan+'">'+v.kd_rekening+' - '+v.nama_rekening+'</option>';
                            }
                        }else{
                            html += '<option value="'+v.kd_rekening+'" data-pasangan="'+v.pasangan+'">'+v.kd_rekening+' - '+v.nama_rekening+'</option>';
                        }
                    });
                    $('.jurnal_modal #namarekening').html(html);
                }
           });
        }else{
            var html = '';
            html += '<option value="">- pilih buku terlebih dahulu</option>';
            $('.jurnal_modal #namarekening').html(html);
        }
    }

    function getPasangan(id){
        
        $.ajax({
            type:'GET',
            url : '{{route("jurnal.get_pasangan",["id"=>null])}}/'+id,
            data:null,
            dataType:'json',
            success:function(i){
               html = '<option value="'+i.pasangan.kd_rekening+'">'+i.pasangan.kd_rekening+' - '+i.pasangan.nama_rekening+'</option>';
                $('.jurnal_modal #namarekeningpasangan').html(html);
                
                var rek = $('.jurnal_modal #namarekening').val()
                if (rek == '151.01' || rek == '141.01' || rek == '111.21' || rek == '161.01') {
                    $('#bukan_inven').hide()
                    $('#ini_inven').show()
                    if (rek == '161.01') {
                        $('#input_umur').hide()
                        $('#input_perolehan').removeClass('col-sm-6')
                        $('#input_perolehan').addClass('col-sm-12')
                        
                        $('#umur').val('360')
                    } else {
                        $('#input_umur').show()
                        $('#input_perolehan').addClass('col-sm-6')
                        $('#input_perolehan').removeClass('col-sm-12')
                        
                        $('#umur').val('')
                    }
                } else {
                    $('#bukan_inven').show()
                    $('#ini_inven').hide()
                }
                //dibalik karena berlawanan dengan kd rekening
                // if(i.pasangan.jenis_mutasi == 'kredit'){
                //     html2 = '<option value="debit">Debit</option>';
                // }else{
                //     html2 = '<option value="kredit">Kredit</option>';
                // }
               
                // $('.jurnal_modal #jenismutasi').html(html2);
            }
       });
    }

    function getHutang(id){
        
        $.ajax({
            type:'GET',
            url : '{{route("jurnal.get_hutang",["id"=>null])}}/'+id,
            data:null,
            dataType:'json',
            success:function(i){
               $('.jurnal_modal #nominal').val(i.tp);
            }
       });
    }
</script>
@endsection
