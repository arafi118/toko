<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('business_id')->unsigned()->index();
			$table->integer('location_id')->unsigned()->index();
			$table->integer('res_table_id')->unsigned()->nullable()->comment('fields to restaurant module');
			$table->integer('res_waiter_id')->unsigned()->nullable()->comment('fields to restaurant module');
			$table->enum('res_order_status', array('received','cooked','served'))->nullable();
			$table->enum('type', array('purchase','sell','expense','stock_adjustment','sell_transfer','purchase_transfer','opening_stock','sell_return','opening_balance','purchase_return'))->nullable()->index();
			$table->enum('status', array('received','pending','ordered','draft','final'));
			$table->boolean('is_quotation')->default(0);
			$table->enum('payment_status', array('paid','due','partial'))->nullable();
			$table->boolean('is_hutang_piutang')->default(0);
			$table->string('kd_rekening_debit', 50)->nullable();
			$table->string('kd_rekening_kredit', 50)->nullable();
			$table->string('kd_rekening_debit_htg_biaya_kirim', 50)->nullable();
			$table->string('kd_rekening_kredit_htg_biaya_kirim', 50)->nullable();
			$table->enum('adjustment_type', array('normal','abnormal'))->nullable();
			$table->integer('contact_id')->unsigned()->nullable()->index();
			$table->integer('customer_group_id')->nullable()->comment('used to add customer group while selling');
			$table->string('invoice_no', 191)->nullable();
			$table->string('ref_no', 191)->nullable();
			$table->dateTime('transaction_date')->index();
			$table->decimal('total_before_tax', 20)->default(0.00);
			$table->integer('tax_id')->unsigned()->nullable()->index('transactions_tax_id_foreign');
			$table->decimal('tax_amount', 20)->default(0.00);
			$table->enum('discount_type', array('fixed','percentage'))->nullable();
			$table->string('discount_amount', 10)->nullable();
			$table->string('shipping_details', 191)->nullable();
			$table->decimal('shipping_charges', 20)->default(0.00);
			$table->text('additional_notes', 65535)->nullable();
			$table->text('staff_note', 65535)->nullable();
			$table->decimal('final_total', 20)->default(0.00);
			$table->float('bayar', 10, 0)->nullable();
			$table->float('kembali', 10, 0)->nullable();
			$table->integer('expense_category_id')->unsigned()->nullable()->index();
			$table->integer('expense_for')->unsigned()->nullable()->index('transactions_expense_for_foreign');
			$table->integer('commission_agent')->nullable();
			$table->string('document', 191)->nullable();
			$table->boolean('is_direct_sale')->default(0);
			$table->decimal('exchange_rate', 20, 3)->default(1.000);
			$table->decimal('total_amount_recovered', 20)->nullable()->comment('Used for stock adjustment.');
			$table->integer('transfer_parent_id')->nullable();
			$table->integer('return_parent_id')->nullable();
			$table->integer('opening_stock_product_id')->nullable();
			$table->integer('created_by')->unsigned()->index();
			$table->integer('pay_term_number')->nullable();
			$table->enum('pay_term_type', array('days','months'))->nullable();
			$table->integer('selling_price_group_id')->nullable();
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
		Schema::drop('transactions');
	}

}
