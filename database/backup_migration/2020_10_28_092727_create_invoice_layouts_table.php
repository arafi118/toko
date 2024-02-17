<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoiceLayoutsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invoice_layouts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191);
			$table->text('header_text', 65535)->nullable();
			$table->string('invoice_no_prefix', 191)->nullable();
			$table->string('quotation_no_prefix', 191)->nullable();
			$table->string('invoice_heading', 191)->nullable();
			$table->string('sub_heading_line1', 191)->nullable();
			$table->string('sub_heading_line2', 191)->nullable();
			$table->string('sub_heading_line3', 191)->nullable();
			$table->string('sub_heading_line4', 191)->nullable();
			$table->string('sub_heading_line5', 191)->nullable();
			$table->string('invoice_heading_not_paid', 191)->nullable();
			$table->string('invoice_heading_paid', 191)->nullable();
			$table->string('quotation_heading', 191)->nullable();
			$table->string('sub_total_label', 191)->nullable();
			$table->string('discount_label', 191)->nullable();
			$table->string('tax_label', 191)->nullable();
			$table->string('total_label', 191)->nullable();
			$table->string('total_due_label', 191)->nullable();
			$table->string('paid_label', 191)->nullable();
			$table->boolean('show_client_id')->default(0);
			$table->string('client_id_label', 191)->nullable();
			$table->string('client_tax_label', 191)->nullable();
			$table->string('date_label', 191)->nullable();
			$table->boolean('show_time')->default(1);
			$table->boolean('show_brand')->default(0);
			$table->boolean('show_sku')->default(1);
			$table->boolean('show_cat_code')->default(1);
			$table->boolean('show_expiry')->default(0);
			$table->boolean('show_lot')->default(0);
			$table->boolean('show_sale_description')->default(0);
			$table->string('sales_person_label', 191)->nullable();
			$table->boolean('show_sales_person')->default(0);
			$table->string('table_product_label', 191)->nullable();
			$table->string('table_qty_label', 191)->nullable();
			$table->string('table_unit_price_label', 191)->nullable();
			$table->string('table_subtotal_label', 191)->nullable();
			$table->string('cat_code_label', 191)->nullable();
			$table->string('logo', 191)->nullable();
			$table->boolean('show_logo')->default(0);
			$table->boolean('show_business_name')->default(0);
			$table->boolean('show_location_name')->default(1);
			$table->boolean('show_landmark')->default(1);
			$table->boolean('show_city')->default(1);
			$table->boolean('show_state')->default(1);
			$table->boolean('show_zip_code')->default(1);
			$table->boolean('show_country')->default(1);
			$table->boolean('show_mobile_number')->default(1);
			$table->boolean('show_alternate_number')->default(0);
			$table->boolean('show_email')->default(0);
			$table->boolean('show_tax_1')->default(1);
			$table->boolean('show_tax_2')->default(0);
			$table->boolean('show_barcode')->default(0);
			$table->boolean('show_payments')->default(0);
			$table->boolean('show_customer')->default(0);
			$table->string('customer_label', 191)->nullable();
			$table->string('highlight_color', 10)->nullable();
			$table->text('footer_text', 65535)->nullable();
			$table->text('module_info', 65535)->nullable();
			$table->boolean('is_default')->default(0);
			$table->integer('business_id')->unsigned()->index('invoice_layouts_business_id_foreign');
			$table->string('design', 256)->nullable()->default('classic');
			$table->string('cn_heading', 191)->nullable()->comment('cn = credit note');
			$table->string('cn_no_label', 191)->nullable();
			$table->string('cn_amount_label', 191)->nullable();
			$table->text('table_tax_headings', 65535)->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('invoice_layouts');
	}

}
