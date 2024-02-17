$(document).ready( function () {
	//Add products
    if($( "#search_product_for_srock_adjustment" ).length > 0){
        //Add Product
		$( "#search_product_for_srock_adjustment" ).autocomplete({
			source: function(request, response) {
	    		$.getJSON("/products/list", { location_id: $('#location_id').val(), term: request.term }, response);
	  			},
			minLength: 2,
			response: function(event,ui) {
				if (ui.content.length == 1)
				{
					ui.item = ui.content[0];
					if(ui.item.qty_available > 0 && ui.item.enable_stock == 1){
						$(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
						$(this).autocomplete('close');
					}
				} else if (ui.content.length == 0)
		            {
		                swal(LANG.no_products_found)
		            }
			},
			focus: function( event, ui ) {
				if(ui.item.qty_available <= 0){
					return false;
				}
			},
			select: function( event, ui ) {
				if(ui.item.qty_available > 0){
					$(this).val(null);
	    			stock_adjustment_product_row(ui.item.variation_id);
				} else{
					alert(LANG.out_of_stock);
				}
			}
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
			if(item.qty_available <= 0){
				
				var string = '<li class="ui-state-disabled">'+ item.name;
				if(item.type == 'variable'){
	        		string += '-' + item.variation;
	        	}
	        	string += ' (' + item.sub_sku + ') (Out of stock) </li>';
	            return $(string).appendTo(ul);
	        } else if(item.enable_stock != 1){
	        	return ul;
	        } 
	        else {
	        	var string =  "<div>" + item.name;
	        	if(item.type == 'variable'){
	        		string += '-' + item.variation;
	        	}
	        	string += ' (' + item.sub_sku + ') </div>';
	    		return $( "<li>" )
	        		.append(string)
	        		.appendTo( ul );
	        }
	    }
    }

    $('select#location_id').change(function(){
		$('table#stock_adjustment_product_table tbody').html('');
		$('#product_row_index').val(0);
	});

	$(document).on( 'change', 'input.stock', function(){
		// update_kuu( $(this).closest('tr') );
	});
	$(document).on( 'change', 'input.product_quantity', function(){
		update_table_row( $(this).closest('tr') );
		update_kuu( $(this).closest('tr') );
		// getKet( $(this).closest('tr') );
	});

	$(document).on( 'click', '.remove_product_row', function(){
		swal({
          title: LANG.sure,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
            	$(this).closest('tr').remove();
				update_table_total();
            }
        });
	});

	//Date picker
    $('#transaction_date').datepicker({
        autoclose: true,
        format: datepicker_date_format
    });
    
    $('#tanggal_so').datepicker({
        autoclose: true,
        format: datepicker_date_format
    });

    $('form#stock_adjustment_form').validate();

    stock_adjustment_table = $('#stock_adjustment_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: '/stock-adjustments',
		columnDefs: [ {
			"targets": 3,
			"orderable": false,
			"searchable": false
		} ],
		columns: [
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'ref_no', name: 'ref_no'},
            { data: 'location_name', name: 'BL.name'},
            // { data: 'additional_notes', name: 'additional_notes'},
            { data: 'action', name: 'action'},
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#purchase_table'));
        }
    });
    var detailRows = [];

    $('#stock_adjustment_table tbody').on( 'click', '.view_stock_adjustment', function () {
        var tr = $(this).closest('tr');
        var row = stock_adjustment_table.row( tr );
        var idx = $.inArray( tr.attr('id'), detailRows );
 
        if ( row.child.isShown() ) {
            $(this).find('i').removeClass( 'fa-eye' ).addClass('fa-eye-slash');
            row.child.hide();
 
            // Remove from the 'open' array
            detailRows.splice( idx, 1 );
        }
        else {
            $(this).find('i').removeClass( 'fa-eye-slash' ).addClass('fa-eye');

            row.child( get_stock_adjustment_details( row.data() ) ).show();
 
            // Add to the 'open' array
            if ( idx === -1 ) {
                detailRows.push( tr.attr('id') );
            }
        }
    } );

    // On each draw, loop over the `detailRows` array and show any child rows
    stock_adjustment_table.on( 'draw', function () {
        $.each( detailRows, function ( i, id ) {
            $('#'+id+' .view_stock_adjustment').trigger( 'click' );
        } );
    } );

    $(document).on('click', 'button.delete_stock_adjustment', function(){
    	swal({
          title: LANG.sure,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
            	var href = $(this).data('href');
            	$.ajax({
					method: "DELETE",
					url: href,
					dataType: "json",
					success: function(result){
						if(result.success){
							toastr.success(result.msg);
							stock_adjustment_table.ajax.reload();
						} else {
							toastr.error(result.msg);
						}
					}
				});
            }
        });
    })
    $(document).on('click', 'button.print_stock_adjustment', function() {
    	var id = $(this).closest('tr').attr('id');
    	window.open(`/stock/${id}/print`);
    	
    })

	// $(document).on( 'change', 'input.selisih', function(){
	// 	getKet( $(this).closest('tr'));
	// });
	
	
	//Table Daftar SO serverside
	daftar_so_table = $('table#daftar_so').DataTable({
		processing: true,
        serverSide: true,
		aaSorting: [[0, 'desc']],
		"ajax": {
			"url": "/penyesuaian/daftar-stock-opname",
			"data": function ( d ){
				d.so_date = $('#tanggal_so').val();
                d.rak_id = $('#rak_id').val();

			}
		},
		columns: [
			{data: 'so_date', name: 't.transaction_date'},
            {data: 'product_name', name: 'p.name'},
           	{data: 'rak', name: 'sb.tempat_simpan'},
            {data: 'ref_no', name: 't.ref_no'},
		]
	});


	$('#rak_id, #tanggal_so').change( function(){
        daftar_so_table.ajax.reload();
        // stock_expiry_report_table.ajax.reload();
    });

	
});

	// function getKet(slsh){
	// 	var sel = slsh.find('input.selisih').val();
	// 	slsh.find('input.jajal').val(sel);
	// }

function stock_adjustment_product_row(variation_id){
	var row_index = parseInt($('#product_row_index').val());
	var location_id = $('select#location_id').val();
	$.ajax({
		method: "POST",
		url: "/stock-adjustments/get_product_row",
		data: {row_index: row_index, variation_id: variation_id, location_id: location_id},
		dataType: "html",
		success: function(result){
			$('table#stock_adjustment_product_table tbody').append(result);
			update_table_total();
			$('#product_row_index').val( row_index + 1);
		}
	});
}

function update_table_total(){
	var table_total = 0;
	$('table#stock_adjustment_product_table tbody tr').each( function(){
		var this_total = parseFloat(__read_number($(this).find('input.product_line_total')));
		if(this_total){
			table_total += this_total;
		}
	});
	$('input#total_amount').val(table_total);
	// $('span#total_adjustment').text(__number_f(table_total));
}
function update_harga_tot(){
	var table_total = 0;
	$('table#stock_adjustment_product_table tbody tr').each( function(){
		var this_total = parseFloat(__read_number($(this).find('input.sat_harga')));
		if(this_total){
			table_total += this_total;
		}
	});
	$('input#total_amount').val(table_total);
	// $('span#total_adjustment').text(__number_f(table_total));
}

function update_table_row( tr ){
	var quantity = parseFloat( __read_number(tr.find('input.stock')));
	var unit_price = parseFloat( __read_number(tr.find('input.product_quantity')));
	var row_total = 0;
	// if( !empty(quantity) && !empty(unit_price)){
	// }
	// row_total = quantity - unit_price;
	row_total = unit_price - quantity;
	if(row_total < 0 ){
		var html = '<option value="Rusak">Rusak</option><option value="Hilang">Hilang</option>';
		// var rr = unit_price;
		
	}else {
		// var rr = row_total;
		var html = '<option value="Kelebihan Droping" selected="">Kelebihan Droping</option>';
	}
	tr.find('input.product_line_total').val(__number_f(row_total));
	tr.find('select.slct').html(html);
	// tr.find('input.jajal').val(__number_f(rr));
	// update_table_total();
}
function update_kuu( tr ){
	var quantity = parseFloat( __read_number(tr.find('input.product_line_total')));
	var unit_price = parseFloat( __read_number(tr.find('input.harga_ku')));
	var row_total = 0;
	if( quantity && unit_price){
		qty = quantity <0 ? quantity*-1 : quantity;
		// if(quantity < 0)
		row_total = qty * unit_price;
	}
	tr.find('input.sat_harga').val(__number_f(row_total));
	update_harga_tot();
}

function get_stock_adjustment_details(rowData){
	var div = $('<div/>')
        .addClass( 'loading' )
        .text( 'Loading...' );
    $.ajax( {
        url: '/stock-adjustments/' + rowData.DT_RowId,
        dataType: 'html',
        success: function ( data ) {
            div
                .html( data )
                .removeClass( 'loading' );
        }
    } );
 
    return div;
}