<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVariationGroupPricesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('variation_group_prices', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('variation_id')->unsigned()->index('variation_group_prices_variation_id_foreign');
			$table->integer('price_group_id')->unsigned()->index('variation_group_prices_price_group_id_foreign');
			$table->decimal('price_inc_tax', 20);
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
		Schema::drop('variation_group_prices');
	}

}
