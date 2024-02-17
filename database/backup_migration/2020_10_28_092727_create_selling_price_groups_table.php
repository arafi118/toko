<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSellingPriceGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('selling_price_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191);
			$table->text('description', 65535)->nullable();
			$table->integer('business_id')->unsigned()->index('selling_price_groups_business_id_foreign');
			$table->integer('customer_group_id')->nullable();
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
		Schema::drop('selling_price_groups');
	}

}
