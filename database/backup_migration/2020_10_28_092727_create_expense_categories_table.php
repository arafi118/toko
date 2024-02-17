<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExpenseCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('expense_categories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191);
			$table->integer('business_id')->unsigned()->index('expense_categories_business_id_foreign');
			$table->string('code', 191)->nullable();
			$table->softDeletes();
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
		Schema::drop('expense_categories');
	}

}
