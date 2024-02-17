<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionSellLinesPurchaseLinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transaction_sell_lines_purchase_lines', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('sell_line_id')->unsigned()->nullable()->comment('id from transaction_sell_lines');
			$table->integer('stock_adjustment_line_id')->unsigned()->nullable()->comment('id from stock_adjustment_lines');
			$table->integer('purchase_line_id')->unsigned()->comment('id from purchase_lines');
			$table->decimal('quantity', 20, 4);
			$table->decimal('qty_returned', 20, 4)->default(0.0000);
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
		Schema::drop('transaction_sell_lines_purchase_lines');
	}

}
