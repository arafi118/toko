<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateResProductModifierSetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('res_product_modifier_sets', function(Blueprint $table)
		{
			$table->integer('modifier_set_id')->unsigned()->index('res_product_modifier_sets_modifier_set_id_foreign');
			$table->integer('product_id')->unsigned()->comment('Table use to store the modifier sets applicable for a product');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('res_product_modifier_sets');
	}

}
