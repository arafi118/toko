<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRekeningTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rekening', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('kd_jb', 3);
			$table->string('kd_rekening', 10)->default('');
			$table->integer('business_id');
			$table->string('nama_rekening', 50);
			$table->string('pasangan', 10);
			$table->date('tgl_awal');
			$table->string('tb2019', 50)->comment('kolom ini di isi dengan data komulatif transaksi sampai dengan akhir tahun 2019 , rekening Laba Rugi (411-514) harus NOL, data hanya digunakan pada tahun bersangkutan dan tidak boleh dihapus/diubah');
			$table->string('awal', 50)->comment('Data ini hanya digunakan pada tahun tgLpakai, dan tidak boleh dirubah/dihapus');
			$table->string('posisi', 1);
			$table->string('jenis_mutasi', 10);
			$table->float('2019', 10, 0)->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('rekening');
	}

}
