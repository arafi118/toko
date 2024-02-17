<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBusinessTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('business', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 256);
			$table->integer('currency_id')->unsigned()->index('business_currency_id_foreign');
			$table->date('start_date')->nullable();
			$table->string('tax_number_1', 100)->nullable();
			$table->string('tax_label_1', 10)->nullable();
			$table->string('tax_number_2', 100)->nullable();
			$table->string('tax_label_2', 10)->nullable();
			$table->integer('default_sales_tax')->unsigned()->nullable()->index('business_default_sales_tax_foreign');
			$table->float('default_profit_percent', 5)->default(0.00);
			$table->integer('owner_id')->unsigned()->index('business_owner_id_foreign');
			$table->string('time_zone', 191)->default('Asia/Kolkata');
			$table->boolean('fy_start_month')->default(1);
			$table->enum('accounting_method', array('fifo','lifo','avco'))->default('fifo');
			$table->decimal('default_sales_discount', 20)->nullable();
			$table->enum('sell_price_tax', array('includes','excludes'))->default('includes');
			$table->string('logo', 191)->nullable();
			$table->string('sku_prefix', 191)->nullable();
			$table->boolean('enable_product_expiry')->default(0);
			$table->enum('expiry_type', array('add_expiry','add_manufacturing'))->default('add_expiry');
			$table->enum('on_product_expiry', array('keep_selling','stop_selling','auto_delete'))->default('keep_selling');
			$table->integer('stop_selling_before')->comment('Stop selling expied item n days before expiry');
			$table->boolean('enable_tooltip')->default(1);
			$table->boolean('purchase_in_diff_currency')->default(0)->comment('Allow purchase to be in different currency then the business currency');
			$table->integer('purchase_currency_id')->unsigned()->nullable();
			$table->decimal('p_exchange_rate', 20, 3)->default(1.000);
			$table->integer('transaction_edit_days')->unsigned()->default(30);
			$table->integer('stock_expiry_alert_days')->unsigned()->default(30);
			$table->text('keyboard_shortcuts', 65535)->nullable();
			$table->text('pos_settings', 65535)->nullable();
			$table->boolean('enable_brand')->default(1);
			$table->boolean('enable_category')->default(1);
			$table->boolean('enable_sub_category')->default(1);
			$table->boolean('enable_price_tax')->default(1);
			$table->boolean('enable_purchase_status')->nullable()->default(1);
			$table->boolean('enable_lot_number')->default(0);
			$table->integer('default_unit')->nullable();
			$table->boolean('enable_racks')->default(0);
			$table->boolean('enable_row')->default(0);
			$table->boolean('enable_position')->default(0);
			$table->boolean('enable_editing_product_from_purchase')->default(1);
			$table->enum('sales_cmsn_agnt', array('logged_in_user','user','cmsn_agnt'))->nullable();
			$table->boolean('item_addition_method')->default(1);
			$table->boolean('enable_inline_tax')->default(1);
			$table->enum('currency_symbol_placement', array('before','after'))->default('before');
			$table->text('enabled_modules', 65535)->nullable();
			$table->string('date_format', 191)->default('m/d/Y');
			$table->enum('time_format', array('12','24'))->default('24');
			$table->text('ref_no_prefixes', 65535)->nullable();
			$table->char('theme_color', 20)->nullable();
			$table->integer('created_by')->nullable();
			$table->text('email_settings', 65535)->nullable();
			$table->text('sms_settings', 65535)->nullable();
			$table->boolean('is_active')->default(1);
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
		Schema::drop('business');
	}

}
