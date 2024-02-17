$(document).ready(function(){
    $('#transaction_date').datetimepicker({
		format: moment_date_format + ' ' + moment_time_format,
		ignoreReadonly: true,
	});
	
	
	$('select#select_location_id').change(function () {
		reset_pos_form();
	});
	
    $('#customer_id').select2({
		ajax: {
			url: '/contacts/customers',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					q: params.term, // search term
					page: params.page
				};
			},
			processResults: function (data) {
				return {
					results: data
				};
			}
		},
		minimumInputLength: 1,
		language: {
			noResults: function () {
				var name = $("#customer_id").data("select2").dropdown.$search.val();
				return '<button type="button" data-name="' + name + '" class="btn btn-link add_new_customer"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' + __translate('add_name_as_new_customer', {
					'name': name
				}) + '</button>';
			}
		},
		escapeMarkup: function (markup) {
			return markup;
		}
	});

    $('#customer_id').on('select2:select', function (e) {

		var data = e.params.data;

        // console.log(data);
		$('#hidden_price_group').val(data.selling_price_group_id);

		if (data.pay_term_number) {
			$('input#pay_term_number').val(data.pay_term_number);
		} else {
			$('input#pay_term_number').val("");
		}

		if (data.pay_term_type) {
			$('#pay_term_type').val(data.pay_term_type);
		} else {
			$('#pay_term_type').val("");
		}
	});

    set_default_customer();
    // set_location();

	//Updates for add sell
	$('select#discount_type, input#discount_amount, input#shipping_charges').change(function () {
		pos_total_row();
	});

	//Direct sell submit
	sell_form = $('form#add_preorder_form');
	if ($('form#edit_sell_form').length) {
		sell_form = $('form#edit_sell_form');
		pos_total_row();
	}
	sell_form_validator = sell_form.validate();

	$('button#submit-sell').click(function () {
		$('input#type').val("3");
		// Check
		// if product is present or not.
		if ($('table#pos_table tbody').find('.product_row').length <= 0) {
			toastr.warning(LANG.no_products_added);
			return false;
		}
		if (sell_form.valid()) {
			sell_form.submit();
		}
	});

	//Update line total and check for quantity not greater than max quantity
	$('table#pos_table tbody').on('change', 'input.pos_quantity', function () {

		if (sell_form_validator) {
			sell_form_validator.element($(this));
		}
		// if (pos_form_validator) {
		// 	pos_form_validator.element($(this));
		// }
		// var max_qty = parseFloat($(this).data('rule-max'));
		var entered_qty = __read_number($(this));

		var tr = $(this).parents('tr');

		var unit_price_inc_tax = __read_number(tr.find('input.pos_unit_price_inc_tax'));
		var line_total = entered_qty * unit_price_inc_tax;

		__write_number(tr.find('input.pos_line_total'), line_total, false, 2);
		tr.find('span.pos_line_total_text').text(__currency_trans_from_en(line_total, true));

		pos_total_row();
	});

	//Remove row on click on remove row
	$('table#pos_table tbody').on('click', 'i.pos_remove_row', function () {
		swal({
			title: LANG.sure,
			icon: "warning",
			buttons: true,
			dangerMode: true,
		}).then((willDelete) => {
			if (willDelete) {
				$(this).parents('tr').remove();
				pos_total_row();
			}
		});
	});

    $("#search_product").autocomplete({
        source: function (request, response) {
            var price_group = '';
            /*if($('#price_group').length > 0){
                price_group = $('#price_group').val();
            }*/
            if ($('#hidden_price_group').val() != '' && $('#hidden_price_group').val() != 0) {
                price_group = $('#hidden_price_group').val();
            } else {
                price_group = $('#default_price_group').val();
            }
            $.getJSON("/products/list", {
                price_group: price_group,
                location_id: $('input#location_id').val(),
                term: request.term
            }, response);
        },
        minLength: 2,
        response: function (event, ui) {
            if (ui.content.length == 1) {
                ui.item = ui.content[0];
                // console.log(ui.item);
                // if (ui.item.qty_available > 0) {
                //     $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                //     $(this).autocomplete('close');
                // }
                $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                $(this).autocomplete('close');
            } else if (ui.content.length == 0) {
                swal(LANG.no_products_found)
            .then((value) => {
                  $('input#search_product').select();
            });
            }
        },
        focus: function (event, ui) {
            if (ui.item.qty_available <= 0) {
                return false;
            }
        },
        select: function (event, ui) {
            $(this).val(null);
            pos_product_row(ui.item.variation_id);
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        
            var string = "<div>" + item.name;
            if (item.type == 'variable') {
                string += '-' + item.variation;
            }

            var selling_price = item.selling_price;
            if (item.variation_group_price) {
                selling_price = item.variation_group_price;
            }

            string += ' (' + item.sub_sku + ')' + "<br> Harga: " + selling_price + "</div>";
            return $("<li>")
                .append(string)
                .appendTo(ul);
        
    };

    $(document).on('click', '.add_new_customer', function () {
		$("#customer_id").select2("close");
		var name = $(this).data('name');
		$('.contact_modal').find('input#name').val(name);
		$('.contact_modal').find('select#contact_type').val('customer').closest('div.contact_type_div').addClass('hide');
		$('.contact_modal').modal('show');
	});

	// $('#depe_po').change(function(){
	// 	// toastr["warning"]("Kolom Hutang Tidak Boleh Kosong!!", "Peringatan")
	// 	flag = true;
	$('#depe_po').maskNumber({
		// reverse: true,
		integer: true
	});

	// });
	$('#depe_po').on('keyup', function () {
		// toastr["warning"]("Kolom Hutang Tidak Boleh Kosong!!", "Peringatan")
		let sum_tot_barang = $('#sum_tot_barang').val();
		let depe_po = $('#depe_po').val();
		let depe_po_conv = __number_uf(depe_po);

		let tot_all = sum_tot_barang - depe_po_conv;
		__write_number($('.payment-amount').first(), tot_all);

		if(depe_po_conv > sum_tot_barang){
			toastr["warning"]("DP Tidak Boleh Melebihi Jumlah Penjualan", "Peringatan")
		}
	});

	
});

function set_default_customer() {
	var default_customer_id = $('#default_customer_id').val();
	var default_customer_name = $('#default_customer_name').val();
	var exists = $('select#customer_id option[value=' + default_customer_id + ']').length;
	if (exists == 0) {
		$("select#customer_id").append($('<option>', {
			value: default_customer_id,
			text: default_customer_name
		}));
	}

	$('select#customer_id').val(default_customer_id).trigger("change");
}

function pos_product_row(variation_id) {

	//Get item addition method
	var item_addtn_method = 0;
	var add_via_ajax = true;

	if ($('#item_addition_method').length) {
		item_addtn_method = $('#item_addition_method').val();
	}

	if (item_addtn_method == 0) {
		add_via_ajax = true;
	} else {

		var is_added = false;

		//Search for variation id in each row of pos table
		$('#pos_table tbody').find('tr').each(function () {

			var row_v_id = $(this).find('.row_variation_id').val();
			var enable_sr_no = $(this).find('.enable_sr_no').val();
			var modifiers_exist = false;
			if ($(this).find('input.modifiers_exist').length > 0) {
				modifiers_exist = true;
			}

			if (row_v_id == variation_id && enable_sr_no !== '1' && !modifiers_exist && !is_added) {
				add_via_ajax = false;
				is_added = true;

				//Increment product quantity
				qty_element = $(this).find('.pos_quantity');
				var qty = __read_number(qty_element);
				__write_number(qty_element, qty + 1);
				qty_element.change();

				round_row_to_iraqi_dinnar($(this));

				$('input#search_product').focus().select();
			}
		});
	}

	if (add_via_ajax) {

		var product_row = $('input#product_row_count').val();
		var location_id = $('input#location_id').val();
		var customer_id = $('select#customer_id').val();
		var is_direct_sell = false;
		if ($('input[name="is_direct_sale"]').length > 0 && $('input[name="is_direct_sale"]').val() == 1) {
			is_direct_sell = true;
		}

		var price_group = '';
		/*if($('#price_group').length > 0){
			price_group = $('#price_group').val();
		}*/

		if ($('#hidden_price_group').val() != '' && $('#hidden_price_group').val() != 0) {
			price_group = $('#hidden_price_group').val();
		} else {
			price_group = $('#default_price_group').val();
		}

		$.ajax({
			method: "GET",
			url: "/preorder/get_product_row/" + variation_id + '/' + location_id,
			async: false,
			data: {
				product_row: product_row,
				customer_id: customer_id,
				is_direct_sell: is_direct_sell,
				price_group: price_group
			},
			dataType: "json",
			success: function (result) {
				if (result.success) {
					$('table#pos_table tbody').append(result.html_content).find('input.pos_quantity');
					//increment row count
					$('input#product_row_count').val(parseInt(product_row) + 1);
					var this_row = $('table#pos_table tbody').find("tr").last();
					pos_each_row(this_row);
					pos_total_row();
					if (result.enable_sr_no == '1') {
						var new_row = $('table#pos_table tbody').find("tr").last();
						new_row.find('.add-pos-row-description').trigger('click');
					}

					round_row_to_iraqi_dinnar(this_row);
					__currency_convert_recursively(this_row)

					$('input#search_product').focus().select();

					//Used in restaurant module
					if (result.html_modifier) {
						$('table#pos_table tbody').find("tr").last().find("td:first").append(result.html_modifier);
					}

				} else {
					swal(result.msg).then((value) => {
						$('input#search_product').focus().select();
					});
				}
			}
		});
	}
}

function pos_each_row(row_obj) {
	var unit_price = __read_number(row_obj.find('input.pos_unit_price'));

	var discounted_unit_price = calculate_discounted_unit_price(row_obj);
	var tax_rate = row_obj.find('select.tax_id').find(':selected').data('rate');

	var unit_price_inc_tax = discounted_unit_price + __calculate_amount('percentage', tax_rate, discounted_unit_price);
	__write_number(row_obj.find('input.pos_unit_price_inc_tax'), unit_price_inc_tax);

	//var unit_price_inc_tax = __read_number(row_obj.find('input.pos_unit_price_inc_tax'));

	__write_number(row_obj.find('input.item_tax'), unit_price_inc_tax - discounted_unit_price);
}

function pos_total_row() {
	var total_quantity = 0;
	var price_total = 0;

	$('table#pos_table tbody tr').each(function () {
		total_quantity = total_quantity + __read_number($(this).find('input.pos_quantity'));
		price_total = price_total + __read_number($(this).find('input.pos_line_total'));
	});

	//Go through the modifier prices.
	$('input.modifiers_price').each(function () {
		price_total = price_total + __read_number($(this));
	});

	//updating shipping charges
	$('span#shipping_charges_amount').text(__currency_trans_from_en(__read_number($('input#shipping_charges_modal')), false));


	$('span.total_quantity').each(function () {
		$(this).html(__number_f(total_quantity));
	});

	//$('span.unit_price_total').html(unit_price_total);
	$('span.price_total').html(__currency_trans_from_en(price_total, false));

	calculate_billing_details(price_total);
}

function calculate_billing_details(price_total) {
	var discount = pos_discount(price_total);
	var order_tax = pos_order_tax(price_total, discount);
	var calculation_type = $('#discount_type').val();

	//Add shipping charges.
	var shipping_charges = __read_number($('input#shipping_charges'));

	if (calculation_type == 'fee') {
		discount = 0;
	}

	var total_payable = price_total + order_tax - discount + shipping_charges;

	__write_number($('input#final_total_input'), total_payable);
	var curr_exchange_rate = 1;
	if ($('#exchange_rate').length > 0 && $('#exchange_rate').val()) {
		curr_exchange_rate = __read_number($('#exchange_rate'));
	}
	var shown_total = total_payable * curr_exchange_rate;
	$('span#total_payable').text(__currency_trans_from_en(shown_total, false));

	$('span.total_payable_span').text(__currency_trans_from_en(total_payable, true));

	//Check if edit form then don't update price.
	if ($('form#edit_pos_sell_form').length == 0) {
		__write_number($('.payment-amount').first(), total_payable);
	}

	$('#sum_tot_barang').val(total_payable);
	calculate_balance_due();
}

function pos_discount(total_amount) {
	var calculation_type = $('#discount_type').val();
	var calculation_amount = __read_number($('#discount_amount'));

	var discount = __calculate_amount(calculation_type, calculation_amount, total_amount);

	$('span#total_discount').text(__currency_trans_from_en(discount, false));

	return discount;
}

function pos_order_tax(price_total, discount) {

	var tax_rate_id = $('#tax_rate_id').val();
	var calculation_type = 'percentage';
	var calculation_amount = __read_number($('#tax_calculation_amount'));
	var total_amount = price_total - discount;

	if (tax_rate_id) {
		var order_tax = __calculate_amount(calculation_type, calculation_amount, total_amount);
	} else {
		var order_tax = 0;
	}

	$('span#order_tax').text(__currency_trans_from_en(order_tax, false));

	return order_tax;
}

function calculate_balance_due() {
	var total_payable = __read_number($('#final_total_input'));
	var total_paying = 0;
	$('#payment_rows_div').find('.payment-amount').each(function () {
		if (parseFloat($(this).val())) {
			total_paying += __read_number($(this));
		}
	});
	var bal_due = total_payable - total_paying;
	var change_return = 0;

	//change_return
	if (bal_due < 0 || Math.abs(bal_due) < 0.05) {
		__write_number($('input#change_return'), bal_due * -1);
		$('span.change_return_span').text(__currency_trans_from_en(bal_due * -1, true));
		change_return = bal_due * -1;
		bal_due = 0;
	} else {
		__write_number($('input#change_return'), 0);
		$('span.change_return_span').text(__currency_trans_from_en(0, true));
		change_return = 0;
	}

	__write_number($('input#total_paying_input'), total_paying);
	$('span.total_paying').text(__currency_trans_from_en(total_paying, true));

	__write_number($('input#in_balance_due'), bal_due);
	$('span.balance_due').text(__currency_trans_from_en(bal_due, true));

	__highlight(bal_due * -1, $('span.balance_due'));
	__highlight(change_return * -1, $('span.change_return_span'));
}

function calculate_discounted_unit_price(row) {
	var this_unit_price = __read_number(row.find('input.pos_unit_price'));
	var row_discounted_unit_price = this_unit_price;
	var row_discount_type = row.find('select.row_discount_type').val();
	var row_discount_amount = __read_number(row.find('input.row_discount_amount'));
	if (row_discount_amount) {
		if (row_discount_type == 'fixed') {
			row_discounted_unit_price = this_unit_price - row_discount_amount;
		} else {
			row_discounted_unit_price = __substract_percent(this_unit_price, row_discount_amount);
		}
	}

	return row_discounted_unit_price;
}

function round_row_to_iraqi_dinnar(row) {
	if (iraqi_selling_price_adjustment) {
		var element = row.find('input.pos_unit_price_inc_tax');
		var unit_price = round_to_iraqi_dinnar(__read_number(element));
		__write_number(element, unit_price);
		element.change();
	}
}

function reset_pos_form() {

	//If on edit page then redirect to Add POS page
	if ($('form#edit_pos_sell_form').length > 0) {
		setTimeout(function () {
			window.location = '/pos/create/';
		}, 4000);
		return true;
	}

	if (sell_form[0]) {
		sell_form[0].reset();
	}
	set_default_customer();
	set_location();

	$('tr.product_row').remove();
	$('span.total_quantity, span.price_total, span#total_discount, span#order_tax, span#total_payable').text(0);
	$('span.total_payable_span', 'span.total_paying', 'span.balance_due').text(0);

	$('#modal_payment').find('.remove_payment_row').each(function () {
		$(this).closest('.payment_row').remove();
	});

	//Reset discount
	__write_number($('input#discount_amount'), $('input#discount_amount').data('default'));
	$('input#discount_type').val($('input#discount_type').data('default'));

	//Reset tax rate
	$('input#tax_rate_id').val($('input#tax_rate_id').data('default'));
	__write_number($('input#tax_calculation_amount'), $('input#tax_calculation_amount').data('default'));

	$('select.payment_types_dropdown').val('cash').trigger('change');
	$('#price_group').trigger('change');
}

function set_location() {
	if ($('select#select_location_id').length == 1) {
		$('input#location_id').val($('select#select_location_id').val());
		$('input#location_id').data('receipt_printer_type', $('select#select_location_id').find(':selected').data('receipt_printer_type'));
	}

	if ($('input#location_id').val()) {
		$('input#search_product').prop("disabled", false).focus();
	} else {
		$('input#search_product').prop("disabled", true);
	}

// 	initialize_printer();
}

