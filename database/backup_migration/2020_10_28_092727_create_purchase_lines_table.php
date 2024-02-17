<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePurchaseLinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('purchase_lines', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('transaction_id')->unsigned()->index('purchase_lines_transaction_id_foreign');
			$table->integer('product_id')->unsigned()->index('purchase_lines_product_id_foreign');
			$table->integer('variation_id')->unsigned()->index('purchase_lines_variation_id_foreign');
			$table->decimal('quantity', 20, 4);
			$table->decimal('pp_without_discount', 20)->default(0.00)->comment('Purchase price before inline discounts');
			$table->decimal('discount_percent', 5)->default(0.00)->comment('Inline discount percentage');
			$table->decimal('purchase_price', 20)->nullable();
			$table->decimal('purchase_price_inc_tax', 20)->default(0.00);
			$table->decimal('item_tax', 20)->nullable();
			$table->integer('tax_id')->unsigned()->nullable()->index('purchase_lines_tax_id_foreign');
			$table->decimal('quantity_sold', 20, 4)->nullable()->default(0.0000);
			$table->decimal('quantity_adjusted', 20, 4)->nullable()->default(0.0000);
			$table->decimal('quantity_returned', 20, 4)->default(0.0000);
			$table->date('mfg_date')->nullable();
			$table->date('exp_date')->nullable();
			$table->string('lot_number', 256)->nullable();
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
		Schema::drop('purchase_lines');
	}

}
