<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoiceSchemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invoice_schemes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('business_id')->unsigned()->index('invoice_schemes_business_id_foreign');
			$table->string('name', 191);
			$table->enum('scheme_type', array('blank','year'));
			$table->string('prefix', 191)->nullable();
			$table->integer('start_number')->nullable();
			$table->integer('invoice_count')->default(0);
			$table->integer('total_digits')->nullable();
			$table->boolean('is_default')->default(0);
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
		Schema::drop('invoice_schemes');
	}

}
