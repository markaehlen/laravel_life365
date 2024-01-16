<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSublocationsTable extends Migration {

	public function up()
	{
		Schema::create('sublocations', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 255);
			$table->integer('location_id')->unsigned();
			$table->boolean('status')->default(0);
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('sublocations');
	}
}