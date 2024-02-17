<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductVariationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_variations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('variation_template_id')->nullable();
			$table->string('name', 191)->index();
			$table->integer('product_id')->unsigned()->index();
			$table->boolean('is_dummy')->default(1);
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
		Schema::drop('product_variations');
	}

}
