<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191)->index();
			$table->integer('business_id')->unsigned()->index();
			$table->enum('type', array('single','variable','modifier'))->nullable();
			$table->integer('unit_id')->unsigned()->nullable()->index();
			$table->integer('brand_id')->unsigned()->nullable()->index('products_brand_id_foreign');
			$table->integer('category_id')->unsigned()->nullable()->index('products_category_id_foreign');
			$table->integer('sub_category_id')->unsigned()->nullable()->unique('sub_category_id');
			$table->integer('tax')->unsigned()->nullable()->index('products_tax_foreign');
			$table->enum('tax_type', array('inclusive','exclusive'));
			$table->boolean('enable_stock')->default(0);
			$table->integer('alert_quantity');
			$table->string('sku', 191);
			$table->enum('barcode_type', array('C39','C128','EAN13','EAN8','UPCA','UPCE'))->nullable()->default('C128');
			$table->decimal('expiry_period', 4)->nullable();
			$table->enum('expiry_period_type', array('days','months'))->nullable();
			$table->boolean('enable_sr_no')->default(0);
			$table->string('weight', 191)->nullable();
			$table->string('product_custom_field1', 191)->nullable();
			$table->string('product_custom_field2', 191)->nullable();
			$table->string('product_custom_field3', 191)->nullable();
			$table->string('product_custom_field4', 191)->nullable();
			$table->string('image', 191)->nullable();
			$table->integer('created_by')->unsigned()->index();
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
		Schema::drop('products');
	}

}
