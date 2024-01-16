<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEcoparametersTable extends Migration {

	public function up()
	{
		Schema::create('ecoparameters', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 255);
			$table->string('section', 255);
			$table->string('type', 255);
			$table->double('default_value');
			$table->string('default_unit', 255);
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('ecoparameters');
	}
}