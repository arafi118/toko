<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionPaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transaction_payments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('transaction_id')->unsigned()->nullable()->index('transaction_payments_transaction_id_foreign');
			$table->integer('business_id')->nullable();
			$table->boolean('is_return')->default(0)->comment('Used during sales to return the change');
			$table->decimal('amount', 20)->default(0.00);
			$table->enum('method', array('cash','card','cheque','bank_transfer','custom_pay_1','custom_pay_2','custom_pay_3','other','tempo'))->nullable();
			$table->string('transaction_no', 191)->nullable();
			$table->string('card_transaction_number', 191)->nullable();
			$table->string('card_number', 191)->nullable();
			$table->string('card_type', 191)->nullable();
			$table->string('card_holder_name', 191)->nullable();
			$table->string('card_month', 191)->nullable();
			$table->string('card_year', 191)->nullable();
			$table->string('card_security', 5)->nullable();
			$table->string('cheque_number', 191)->nullable();
			$table->string('bank_account_number', 191)->nullable();
			$table->dateTime('paid_on')->nullable();
			$table->integer('created_by')->index();
			$table->integer('payment_for')->nullable();
			$table->integer('parent_id')->nullable();
			$table->string('note', 191)->nullable();
			$table->string('payment_ref_no', 191)->nullable();
			$table->integer('account_id')->nullable();
			$table->string('id_rekening_debit')->nullable();
			$table->string('id_rekening_kredit')->nullable();
			$table->string('id_jenis_buku')->nullable();
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
		Schema::drop('transaction_payments');
	}

}
