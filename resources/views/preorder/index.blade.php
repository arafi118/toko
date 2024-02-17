@extends('layouts.app')
@section('title', 'Daftar Pre-Order')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>Pre-Order
        <small></small>
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
	<div class="box">
        <div class="box-header">
        	<h3 class="box-title">Daftar Pre-Order</h3>
            @can('sell.create')
            	<div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('PreOrderController@create')}}">
    				<i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endcan
            
        </div>
        <div class="box-body">
            @can('direct_sell.access')
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="input-group">
                              <button type="button" class="btn btn-primary" id="sell_date_filter">
                                <span>
                                  <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                                </span>
                                <i class="fa fa-caret-down"></i>
                              </button>
                            </div>
                          </div>
                    </div>
                </div>
                
                <div class="table-responsive">
            	<table class="table table-bordered table-striped ajax_view" id="sell_table">
            		<thead>
            			<tr>
            				<th>@lang('messages.date')</th>
                            <th>@lang('sale.invoice_no')</th>
    						<th>@lang('sale.customer_name')</th>
                            <th>@lang('sale.location')</th>
                            <th>@lang('sale.payment_status')</th>
    						<th>@lang('sale.total_amount')</th>
                           {{-- <th>@lang('sale.total_paid')</th> --}}
                           <th>Sisa Pelunasan</th>
    						<th>@lang('messages.action')</th>
            			</tr>
            		</thead>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-center">
                            <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                            <td id="footer_payment_status_count"></td>
                            <td><span class="display_currency" id="footer_sale_total" data-currency_symbol ="true"></span></td>
                            <!-- <td><span class="display_currency" id="footer_total_paid" data-currency_symbol ="true"></span></td>
                             --><td><span class="display_currency" id="footer_total_remaining" data-currency_symbol ="true"></span></td>
                            <td></td>
                        </tr>
                    </tfoot>
            	</table>
                </div>
            @endcan
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<!-- This will be printed -->
<!-- <section class="invoice print_section" id="receipt_section">
</section> -->

@stop

@section('javascript')


<script type="text/javascript">
$(document).ready( function(){
    //Date range as a button
    $('#sell_date_filter').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#sell_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            sell_table.ajax.reload();
        }
    );
    $('#sell_date_filter').on('cancel.daterangepicker', function(ev, picker) {
        $('#sell_date_filter').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
        sell_table.ajax.reload();
    });

    sell_table = $('#sell_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        "ajax": {
            "url": "/preorder",
            "data": function ( d ) {
                var start = $('#sell_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end = $('#sell_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                d.start_date = start;
                d.end_date = end;
                d.is_direct_sale = 1;
            }
        },
        columnDefs: [ {
            "targets": [6, 7],
            "orderable": false,
            "searchable": false
        } ],
        columns: [
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'invoice_no', name: 'invoice_no'},
            { data: 'name', name: 'contacts.name'},
            { data: 'business_locations', name: 'bl.name'},
            { data: 'payment_status', name: 'payment_status'},
            { data: 'final_total', name: 'final_total'},
        //    { data: 'total_paid', name: 'total_paid'},
            { data: 'total_remaining', name: 'total_remaining'},
            { data: 'action', name: 'action'}
        ],
        
        "fnDrawCallback": function (oSettings) {

            $('#footer_sale_total').text(sum_table_col($('#sell_table'), 'final-total'));
            
            $('#footer_total_paid').text(sum_table_col($('#sell_table'), 'total-paid'));

            $('#footer_total_remaining').text(sum_table_col($('#sell_table'), 'total-remaining'));

            $('#footer_payment_status_count').html(__sum_status_html($('#sell_table'), 'payment-status-label'));

            __currency_convert_recursively($('#sell_table'));
        },
        createdRow: function( row, data, dataIndex ) {
            // $( row ).find('td:eq(4)').attr('class', 'clickable_td');
        }
    });

    $(document).on('click', '.cancel-pre-order', function(e){
		e.preventDefault();
		swal({
          title: "Batalkan Pre-Order?",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
            	var href = $(this).attr('href');
            	$.ajax({
					method: "POST",
					url: href,
					dataType: "json",
					success: function(result){
						if(result.success == true){
							toastr.success(result.msg);
							if (typeof sell_table !== 'undefined') {
								sell_table.ajax.reload();
							}
							//Displays list of recent transactions
							// if (typeof get_recent_transactions !== 'undefined') {
							// 	get_recent_transactions('final', $('div#tab_final'));
							// 	get_recent_transactions('draft', $('div#tab_draft'));
							// }
							
						} else {
							toastr.error(result.msg);
						}
					}
				});
            }
        });
	});
});
</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection