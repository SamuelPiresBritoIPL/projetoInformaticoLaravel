<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadeiraTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cadeira', function(Blueprint $table)
		{
			$table->unsignedInteger('id',true);
			$table->string('codigo', 45)->nullable();
			$table->string('nome', 60);
			$table->string('abreviatura', 10)->nullable();
			$table->integer('idPlanoCurricular')->unsigned()->index('idPlanoCurricular_idx');
			$table->engine = 'InnoDB';
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cadeira');
	}

}
