<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBusinessLocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('business_locations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('business_id')->unsigned()->index();
			$table->string('location_id', 191)->nullable();
			$table->string('name', 256);
			$table->text('landmark', 65535)->nullable();
			$table->string('country', 100);
			$table->string('state', 100);
			$table->string('city', 100);
			$table->char('zip_code', 7);
			$table->integer('invoice_scheme_id')->unsigned()->index('business_locations_invoice_scheme_id_foreign');
			$table->integer('invoice_layout_id')->unsigned()->index('business_locations_invoice_layout_id_foreign');
			$table->boolean('print_receipt_on_invoice')->nullable()->default(1);
			$table->enum('receipt_printer_type', array('browser','printer'))->default('browser');
			$table->integer('printer_id')->nullable();
			$table->string('mobile', 191)->nullable();
			$table->string('alternate_number', 191)->nullable();
			$table->string('email', 191)->nullable();
			$table->string('website', 191)->nullable();
			$table->string('custom_field1', 191)->nullable();
			$table->string('custom_field2', 191)->nullable();
			$table->string('custom_field3', 191)->nullable();
			$table->string('custom_field4', 191)->nullable();
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
		Schema::drop('business_locations');
	}

}
