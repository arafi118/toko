$(document).ready(function(){

	var start = $('input[name="date-filter"]:checked').data('start');
	var end = $('input[name="date-filter"]:checked').data('end');
	update_statistics(start, end);
	$(document).on('change', 'input[name="date-filter"]', function(){
		var start = $('input[name="date-filter"]:checked').data('start');
		var end = $('input[name="date-filter"]:checked').data('end');
		update_statistics(start, end);
		produk_terlaris.ajax.reload();
		purchase_supplier.ajax.reload();
	});

// Produk terpopuler
	produk_terlaris = $('#produk_terlaris').DataTable({
		processing: true,
		serverSide: true,
		ordering: false,
		searching: false,
		pageLength : 5,
		dom: 'tirp',
		"ajax": {
			"url": "/home/get-produk-populer",
			"data": function ( d ) {
				// d.exp_date_filter = $('#stock_expiry_alert_days').val();
				var start_date = $('input[name="date-filter"]:checked').data('start');
				var end_date = $('input[name="date-filter"]:checked').data('end');
				d.start_date = start_date;
				d.end_date = end_date;
			}
		},
		// "order": [[ 3, "asc" ]],
		columns: [
			{data: 'product', name: 'p.name'},
			{data: 'total_unit_sold', name: 'total_unit_sold'},
			{data: 'stock', name: 'stock'}
			// {data: 'exp_date', name: 'exp_date'},
		]
	});
	
	
	
// 	Purchase Supplier
	purchase_supplier = $('#daftar_beli').DataTable({
		processing: true,
		serverSide: true,
		// ordering: false,
		// searching: true,
		// pageLength : 5,
		// dom: 'tirp',
		aaSorting: [[2, 'desc']],
		"ajax": {
			"url": "/home/get-purchase-supplier",
			"data": function ( d ) {
				// d.exp_date_filter = $('#stock_expiry_alert_days').val();
				var start_date = $('input[name="date-filter"]:checked').data('start');
				var end_date = $('input[name="date-filter"]:checked').data('end');
				d.start_date = start_date;
				d.end_date = end_date;
			}
		},
		// "order": [[ 3, "asc" ]],
		columns: [
			{data: 'product_name', name: 'product_name'},
			{data: 'sup_name', name: 'sup_name'},
			{data: 'transaction_date', name: 'transaction_date'}
			// {data: 'exp_date', name: 'exp_date'},
		]
	});
	

	//atock alert datatables
	var stock_alert_table = $('#stock_alert_table').DataTable({
					processing: true,
					serverSide: true,
					ordering: false,
					searching: false,
					dom: 'tirp',
					buttons:[],
					ajax: '/home/product-stock-alert'
			    });
	//payment dues datatables
	var purchase_payment_dues_table = $('#purchase_payment_dues_table').DataTable({
					processing: true,
					serverSide: true,
				// 	ordering: false,
				// 	searching: false,
				// 	dom: 'tirp',
				// 	buttons:[],
					ajax: '/home/purchase-payment-dues',
					"fnDrawCallback": function (oSettings) {
					    var hut_dua = sum_table_col($('#purchase_payment_dues_table'), 'hutang-dua');
            			$('#footer_hut_beli').text(hut_dua);
			            __currency_convert_recursively($('#purchase_payment_dues_table'));
			        }
			    });

	//Sales dues datatables
	 sales_payment_dues_table = $('#sales_payment_dues_table').DataTable({
					processing: true,
					serverSide: true,
					// ordering: true,
					// searching: true,
					// dom: 'lrtip',
					aaSorting: [[3, 'asc']],
					// pageLength: 5,
					// buttons:[],
					ajax: '/home/sales-payment-dues',
					columns: [
						{data: 'customer', name: 'c.name'},
						{data: 'invoice_no', name: 'transactions.invoice_no'},
						{data: 'due', name: 'due', searchable: false},
						{data: 'transaction_date', name: 'transaction_date'}
					],
					// columnDefs: [{
					// 	'type' : 'date',
					// 	'targets' : '2'
					// }
					// ],
					"fnDrawCallback": function (oSettings) {
					    var hut_satu = sum_table_col($('#sales_payment_dues_table'), 'hutang-satu');
            			$('#footer_tot_hut').text(hut_satu);
			            __currency_convert_recursively($('#sales_payment_dues_table'));
			        }
			    });

	//Stock expiry report table
    stock_expiry_alert_table = $('#stock_expiry_alert_table').DataTable({
                    processing: true,
					serverSide: true,
					searching: false,
					dom: 'tirp',
                    "ajax": {
                        "url": "/reports/stock-expiry",
                        "data": function ( d ) {
                            d.exp_date_filter = $('#stock_expiry_alert_days').val();
                        }
                    },
                    "order": [[ 3, "asc" ]],
                    columns: [
                        {data: 'product', name: 'p.name'},
                        {data: 'location', name: 'l.name'},
                        {data: 'stock_left', name: 'stock_left'},
                        {data: 'exp_date', name: 'exp_date'},
                    ],
                    "fnDrawCallback": function (oSettings) {
                        __show_date_diff_for_human($('#stock_expiry_alert_table'));
                    }
                });
});

function update_statistics( start, end ){
	var data = { start: start, end: end };
	//get purchase details
	var loader = '<i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i>';
	$('.total_purchase').html(loader);
	$('.purchase_due').html(loader);
	$('.total_sell').html(loader);
	$('.invoice_due').html(loader);
	$.ajax({
		method: "POST",
		url: '/home/get-purchase-details',
		dataType: "json",
		data: data,
		success: function(data){
			$('.total_purchase').html(__currency_trans_from_en(data.total_purchase_inc_tax, true ));
			$('.purchase_due').html( __currency_trans_from_en(data.purchase_due, true));
		}
	});
	//get sell details
	$.ajax({
		method: "POST",
		url: '/home/get-sell-details',
		dataType: "json",
		data: data,
		success: function(data){
			$('.total_sell').html(__currency_trans_from_en(data.total_sell_inc_tax, true ));
			$('.invoice_due').html( __currency_trans_from_en(data.invoice_due, true));
		}
	});
}