<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToInscricaoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('inscricao', function(Blueprint $table)
		{
			$table->foreign('idTurno', 'idTurnoo')->references('id')->on('turno')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idUtilizador', 'idUtilizadorr')->references('id')->on('utilizador')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('inscricao', function(Blueprint $table)
		{
			$table->dropForeign('idTurnoo');
			$table->dropForeign('idUtilizadorr');
		});
	}

}
