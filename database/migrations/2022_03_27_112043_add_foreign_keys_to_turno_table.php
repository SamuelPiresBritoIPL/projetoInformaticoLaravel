<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTurnoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('turno', function(Blueprint $table)
		{
			$table->foreign('idCadeira', 'idCadeira_2')->references('id')->on('cadeira')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
		Schema::table('turno', function(Blueprint $table)
		{
			$table->dropForeign('idCadeira_2');
			$table->dropForeign('idUtilizador7');
		});
	}

}
