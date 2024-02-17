<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notification_templates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('business_id');
			$table->string('template_for', 191);
			$table->text('email_body', 65535)->nullable();
			$table->text('sms_body', 65535)->nullable();
			$table->string('subject', 191)->nullable();
			$table->boolean('auto_send')->default(0);
			$table->boolean('auto_send_sms')->default(0);
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
		Schema::drop('notification_templates');
	}

}
