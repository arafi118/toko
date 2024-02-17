<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupSubTaxesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('group_sub_taxes', function(Blueprint $table)
		{
			$table->integer('group_tax_id')->unsigned()->index('group_sub_taxes_group_tax_id_foreign');
			$table->integer('tax_id')->unsigned()->index('group_sub_taxes_tax_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('group_sub_taxes');
	}

}
