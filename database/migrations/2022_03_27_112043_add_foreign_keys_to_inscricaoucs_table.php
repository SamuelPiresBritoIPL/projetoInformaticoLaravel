<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToInscricaoucsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('inscricaoucs', function(Blueprint $table)
		{
			$table->foreign('idCadeira', 'idCadeira_3')->references('id')->on('cadeira')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idUtilizador', 'idUtilizador_4')->references('id')->on('utilizador')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idAnoletivo', 'idAnoletivo_3')->references('id')->on('anoletivo')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('inscricaoucs', function(Blueprint $table)
		{
			$table->dropForeign('idCadeira_3');
			$table->dropForeign('idUtilizador_4');
			$table->dropForeign('idAnoletivo_3');
		});
	}

}
