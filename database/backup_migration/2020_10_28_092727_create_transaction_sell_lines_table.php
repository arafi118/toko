<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionSellLinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transaction_sell_lines', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('transaction_id')->unsigned()->index('transaction_sell_lines_transaction_id_foreign');
			$table->integer('product_id')->unsigned()->index('transaction_sell_lines_product_id_foreign');
			$table->integer('variation_id')->unsigned()->index('transaction_sell_lines_variation_id_foreign');
			$table->decimal('quantity', 20, 4);
			$table->decimal('quantity_returned', 20, 4)->default(0.0000);
			$table->decimal('unit_price_before_discount', 20)->default(0.00);
			$table->decimal('unit_price', 20)->nullable();
			$table->enum('line_discount_type', array('fixed','percentage'))->nullable();
			$table->decimal('line_discount_amount', 20)->default(0.00);
			$table->decimal('unit_price_inc_tax', 20)->nullable();
			$table->decimal('item_tax', 20)->nullable();
			$table->integer('tax_id')->unsigned()->nullable()->index('transaction_sell_lines_tax_id_foreign');
			$table->integer('lot_no_line_id')->nullable();
			$table->text('sell_line_note', 65535)->nullable();
			$table->integer('parent_sell_line_id')->nullable();
			$table->string('id_rekening_debit', 50)->nullable();
			$table->string('id_rekening_kredit', 50)->nullable();
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
		Schema::drop('transaction_sell_lines');
	}

}
