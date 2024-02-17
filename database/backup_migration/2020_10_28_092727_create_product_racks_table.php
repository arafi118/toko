<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductRacksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_racks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('business_id')->unsigned();
			$table->integer('location_id')->unsigned();
			$table->integer('product_id')->unsigned();
			$table->string('rack', 191)->nullable();
			$table->string('row', 191)->nullable();
			$table->string('position', 191)->nullable();
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
		Schema::drop('product_racks');
	}

}
