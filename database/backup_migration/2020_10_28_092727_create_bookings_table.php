<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bookings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contact_id')->unsigned()->index('bookings_contact_id_foreign');
			$table->integer('waiter_id')->unsigned()->nullable();
			$table->integer('table_id')->unsigned()->nullable();
			$table->integer('correspondent_id')->nullable();
			$table->integer('business_id')->unsigned()->index('bookings_business_id_foreign');
			$table->integer('location_id')->unsigned();
			$table->dateTime('booking_start');
			$table->dateTime('booking_end');
			$table->integer('created_by')->unsigned()->index('bookings_created_by_foreign');
			$table->enum('booking_status', array('booked','completed','cancelled'));
			$table->text('booking_note', 65535)->nullable();
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
		Schema::drop('bookings');
	}

}
