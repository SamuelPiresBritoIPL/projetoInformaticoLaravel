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
			$table->integer('codigo');
			$table->smallInteger('ano')->default(1);
			$table->smallInteger('semestre')->default(1);
			$table->string('nome', 150);
			$table->string('abreviatura', 10)->nullable();
			$table->integer('idCurso')->unsigned()->index('idPlanoCurricular_idx');
			$table->string('anoletivo', 10)->nullable();
			$table->timestamps();
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
