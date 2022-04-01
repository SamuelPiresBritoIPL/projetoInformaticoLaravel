<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAberturasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('aberturas', function(Blueprint $table)
		{
			$table->foreign('idUtilizador', 'idUtilizador')->references('id')->on('utilizador')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idCurso', 'idcursoo')->references('id')->on('curso')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idAnoletivo', 'idAnoletivo_1')->references('id')->on('anoletivo')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('aberturas', function(Blueprint $table)
		{
			$table->dropForeign('idUtilizador');
			$table->dropForeign('idcursoo');
			$table->dropForeign('idAnoletivo_1');
		});
	}

}
