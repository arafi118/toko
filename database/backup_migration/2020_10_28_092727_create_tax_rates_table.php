<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaxRatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tax_rates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('business_id')->unsigned()->index('tax_rates_business_id_foreign');
			$table->string('name', 191);
			$table->float('amount');
			$table->boolean('is_tax_group')->default(0);
			$table->integer('created_by')->unsigned()->index('tax_rates_created_by_foreign');
			$table->softDeletes();
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
		Schema::drop('tax_rates');
	}

}
