<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVariationLocationDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('variation_location_details', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('product_id')->unsigned()->index();
			$table->integer('product_variation_id')->unsigned()->index()->comment('id from product_variations table');
			$table->integer('variation_id')->unsigned()->index();
			$table->integer('location_id')->unsigned()->index('variation_location_details_location_id_foreign');
			$table->decimal('qty_available', 20, 4);
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
		Schema::drop('variation_location_details');
	}

}
