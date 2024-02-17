<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCashRegisterTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cash_register_transactions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('cash_register_id')->unsigned()->index('cash_register_transactions_cash_register_id_foreign');
			$table->decimal('amount', 20)->default(0.00);
			$table->enum('pay_method', array('cash','card','cheque','bank_transfer','custom_pay_1','custom_pay_2','custom_pay_3','other'))->nullable();
			$table->enum('type', array('debit','credit'));
			$table->enum('transaction_type', array('initial','sell','transfer','refund'));
			$table->integer('transaction_id')->nullable();
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
		Schema::drop('cash_register_transactions');
	}

}
