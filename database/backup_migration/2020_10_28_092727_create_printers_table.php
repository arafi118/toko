<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePrintersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('printers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('business_id')->unsigned()->index('printers_business_id_foreign');
			$table->string('name', 256);
			$table->enum('connection_type', array('network','windows','linux'));
			$table->enum('capability_profile', array('default','simple','SP2000','TEP-200M','P822D'))->default('default');
			$table->string('char_per_line', 191)->nullable();
			$table->string('ip_address', 191)->nullable();
			$table->string('port', 191)->nullable();
			$table->string('path', 191)->nullable();
			$table->integer('created_by')->unsigned();
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
		Schema::drop('printers');
	}

}
