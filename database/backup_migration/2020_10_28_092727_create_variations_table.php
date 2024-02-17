<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVariationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('variations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191)->index();
			$table->integer('product_id')->unsigned()->index('variations_product_id_foreign');
			$table->string('sub_sku', 191)->nullable()->index();
			$table->integer('product_variation_id')->unsigned()->index('variations_product_variation_id_foreign');
			$table->integer('variation_value_id')->nullable();
			$table->decimal('default_purchase_price', 20)->nullable();
			$table->decimal('dpp_inc_tax', 20)->default(0.00);
			$table->decimal('profit_percent', 20)->default(0.00);
			$table->decimal('default_sell_price', 20)->nullable();
			$table->decimal('sell_price_inc_tax', 20)->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('variations');
	}

}
