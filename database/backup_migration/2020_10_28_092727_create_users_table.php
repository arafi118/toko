<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->char('surname', 10)->nullable();
			$table->string('first_name', 191);
			$table->string('last_name', 191)->nullable();
			$table->string('username', 191)->unique();
			$table->string('initial', 50)->nullable();
			$table->string('email', 191)->nullable();
			$table->string('password', 191);
			$table->char('language', 4)->default('en');
			$table->char('contact_no', 15)->nullable();
			$table->text('address', 65535)->nullable();
			$table->string('remember_token', 100)->nullable();
			$table->integer('business_id')->unsigned()->nullable()->index('users_business_id_foreign');
			$table->boolean('is_cmmsn_agnt')->default(0);
			$table->decimal('cmmsn_percent', 4)->default(0.00);
			$table->boolean('selected_contacts')->default(0);
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
		Schema::drop('users');
	}

}
