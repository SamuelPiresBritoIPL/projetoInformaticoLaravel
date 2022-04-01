<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAulaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('aula', function(Blueprint $table)
		{
			$table->foreign('idTurno', 'idTurno')->references('id')->on('turno')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idProfessor', 'idUtilizador7')->references('id')->on('utilizador')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('aula', function(Blueprint $table)
		{
			$table->dropForeign('idTurno');
		});
	}

}
