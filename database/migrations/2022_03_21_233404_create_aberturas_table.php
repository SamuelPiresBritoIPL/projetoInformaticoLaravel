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
			$table->integer('id')->primary();
			$table->dateTime('dataAbertura');
			$table->dateTime('dataEncerar');
			$table->smallInteger('ano')->default(1);
			$table->smallInteger('tipoAbertura')->default(1);
			$table->integer('idUtilizador')->index('idUtilizador_idx');
			$table->integer('idCurso')->index('idcursoo_idx');
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
