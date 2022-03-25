<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanocurricularTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('planocurricular', function(Blueprint $table)
		{
			$table->unsignedInteger('id',true);
			$table->string('ano', 45)->nullable();
			$table->integer('idCurso')->unsigned()->index('idCurso_idx');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('planocurricular');
	}

}
