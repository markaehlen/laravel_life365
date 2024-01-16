<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateForeignKeys extends Migration {

	public function up()
	{
		Schema::table('units', function(Blueprint $table) {
			$table->foreign('system_id')->references('id')->on('systems')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('sublocations', function(Blueprint $table) {
			$table->foreign('location_id')->references('id')->on('locations')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
	}

	public function down()
	{
		Schema::table('units', function(Blueprint $table) {
			$table->dropForeign('units_system_id_foreign');
		});
		Schema::table('sublocations', function(Blueprint $table) {
			$table->dropForeign('sublocations_location_id_foreign');
		});
	}
}