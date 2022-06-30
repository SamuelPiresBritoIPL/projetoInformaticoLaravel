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
			$table->unsignedInteger('id',true);
			$table->string('idAntigo', 15)->nullable();
			$table->date('data');
			$table->time('horaInicio')->nullable();
			$table->time('horaFim')->nullable();
			$table->integer('idTurno')->unsigned()->index('idTurno_idx');
			$table->integer('idProfessor')->unsigned()->nullable()->index('idUtilizador7_idx');
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
		Schema::drop('aula');
	}

}
