<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCashRegistersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cash_registers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('business_id')->unsigned()->index('cash_registers_business_id_foreign');
			$table->integer('user_id')->unsigned()->nullable()->index('cash_registers_user_id_foreign');
			$table->enum('status', array('close','open'))->default('open');
			$table->dateTime('closed_at')->nullable();
			$table->decimal('closing_amount', 20)->default(0.00);
			$table->integer('total_card_slips')->default(0);
			$table->integer('total_cheques')->default(0);
			$table->text('closing_note', 65535)->nullable();
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
		Schema::drop('cash_registers');
	}

}
