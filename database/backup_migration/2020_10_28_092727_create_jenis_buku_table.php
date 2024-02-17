<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJenisBukuTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('jenis_buku', function(Blueprint $table)
		{
			$table->integer('id')->primary();
			$table->integer('business_id');
			$table->string('unit', 1)->default('1')->comment('Dana Bergulir (1), Sektor Riil (2), Penjaminan (3), UPK-Pay (4), Minimarket (5), Apotek (6)');
			$table->string('posisi', 1)->comment('Aktiva (1), Hutang(2), Modal (3), Pendapatan (4), Biaya (5)');
			$table->string('kd_jr', 2);
			$table->string('kd_jb', 20);
			$table->string('ins', 3);
			$table->string('nama_jb', 50);
			$table->string('icon', 50);
			$table->integer('ap')->default(0)->comment('Apakah termasuk Aset Produktif (Kas, Bank, Produk). Ya(1), Tidak (0)');
			$table->string('file', 50);
			$table->text('lokasi', 65535);
			$table->integer('kd_kab')->default(0);
			$table->text('kecuali', 65535);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('jenis_buku');
	}

}
