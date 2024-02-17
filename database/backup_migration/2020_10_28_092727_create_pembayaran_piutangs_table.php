<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePembayaranPiutangsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pembayaran_piutangs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('id_payment');
			$table->integer('business_id');
			$table->date('tgl_bayar');
			$table->string('cara_bayar', 50)->nullable();
			$table->string('no_rekening', 191)->nullable();
			$table->string('atas_nama_rekening', 191)->nullable();
			$table->string('kd_invoice', 191)->nullable();
			$table->string('kd_buku', 191)->nullable();
			$table->string('kd_rekening_debit', 191)->nullable();
			$table->string('kd_rekening_kredit', 191)->nullable();
			$table->float('nominal', 10, 0);
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
		Schema::drop('pembayaran_piutangs');
	}

}
