<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAberturasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('aberturas', function(Blueprint $table)
		{
			$table->unsignedInteger('id',true);
			$table->dateTime('dataAbertura');
			$table->dateTime('dataEncerar');
			$table->smallInteger('ano')->default(1);
			$table->smallInteger('tipoAbertura')->default(1);
			$table->smallInteger('semestre')->default(1);
			$table->integer('idUtilizador')->unsigned()->index('idUtilizador_idx');
			$table->integer('idCurso')->unsigned()->index('idcursoo_idx');
			$table->integer('idAnoletivo')->unsigned()->index('idAnoletivo_1idx');
			$table->timestamps();
			$table->softDeletes();
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
		Schema::drop('aberturas');
	}

}
