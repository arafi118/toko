<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJurnalTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('jurnal', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('business_id');
			$table->integer('kd_jenis_buku');
			$table->enum('jenis_mutasi', array('debit','kredit'));
			$table->string('kd_rekening_debit');
			$table->string('kd_rekening_kredit')->nullable();
			$table->date('tanggal_jurnal');
			$table->text('keterangan', 65535)->nullable();
			$table->float('nominal', 10, 0);
			$table->integer('ref_id')->nullable();
			$table->integer('created_by');
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
		Schema::drop('jurnal');
	}

}
