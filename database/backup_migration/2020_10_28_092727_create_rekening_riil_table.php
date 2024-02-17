<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRekeningRiilTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rekening_riil', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('idrekening');
			$table->string('kd_rr', 2);
			$table->string('nama_rr', 50);
			$table->integer('posisi');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('rekening_riil');
	}

}
