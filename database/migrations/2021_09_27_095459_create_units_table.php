<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUnitsTable extends Migration {

	public function up()
	{
		Schema::create('units', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('system_id')->unsigned();
			$table->string('name', 255);
			$table->string('type', 255);
			$table->double('conversion_factor')->default('1');
			$table->boolean('status')->default(0);
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('units');
	}
}