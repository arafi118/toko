<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBarcodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('barcodes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191);
			$table->text('description', 65535)->nullable();
			$table->float('width')->nullable();
			$table->float('height')->nullable();
			$table->float('paper_width')->nullable();
			$table->float('paper_height')->nullable();
			$table->float('top_margin')->nullable();
			$table->float('left_margin')->nullable();
			$table->float('row_distance')->nullable();
			$table->float('col_distance')->nullable();
			$table->integer('stickers_in_one_row')->nullable();
			$table->boolean('is_default')->default(0);
			$table->boolean('is_continuous')->default(0);
			$table->integer('stickers_in_one_sheet')->nullable();
			$table->integer('business_id')->unsigned()->nullable()->index('barcodes_business_id_foreign');
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
		Schema::drop('barcodes');
	}

}
