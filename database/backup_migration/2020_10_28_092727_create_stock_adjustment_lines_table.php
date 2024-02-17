<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStockAdjustmentLinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stock_adjustment_lines', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('transaction_id')->unsigned()->index();
			$table->integer('product_id')->unsigned()->index('stock_adjustment_lines_product_id_foreign');
			$table->integer('variation_id')->unsigned()->index('stock_adjustment_lines_variation_id_foreign');
			$table->decimal('quantity', 20, 4);
			$table->decimal('unit_price', 20)->nullable()->comment('Last purchase unit price');
			$table->integer('removed_purchase_line')->nullable();
			$table->integer('lot_no_line_id')->nullable();
			$table->string('kurang_tambah', 15)->nullable();
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
		Schema::drop('stock_adjustment_lines');
	}

}
