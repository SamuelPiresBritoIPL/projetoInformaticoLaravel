<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPedidosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pedidos', function(Blueprint $table)
		{
			$table->foreign('idUtilizador', 'idUtilizador_')->references('id')->on('utilizador')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idAnoletivo', 'idAnoletivo_5')->references('id')->on('anoletivo')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idCurso', 'idCurso_ibfk_4')->references('id')->on('curso')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pedidos', function(Blueprint $table)
		{
			$table->dropForeign('idUtilizador_');
			$table->dropForeign('idAnoletivo_5');
			$table->dropForeign('idCurso_ibfk_4');
		});
	}

}
