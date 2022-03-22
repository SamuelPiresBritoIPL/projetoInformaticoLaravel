<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAulaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('aula', function(Blueprint $table)
		{
			$table->integer('id')->primary();
			$table->smallInteger('diaSemana')->default(0);
			$table->time('horaInicio');
			$table->time('horaFim');
			$table->integer('idTurno')->index('idTurno_idx');
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
		Schema::drop('aula');
	}

}
