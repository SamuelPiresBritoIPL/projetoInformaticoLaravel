<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPedidosucsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pedidosucs', function(Blueprint $table)
		{
			$table->foreign('idCadeira', 'idCadeira__')->references('id')->on('cadeira')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('idPedidos', 'idPedidos')->references('id')->on('pedidos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pedidosucs', function(Blueprint $table)
		{
			$table->dropForeign('idCadeira__');
			$table->dropForeign('idPedidos');
		});
	}

}
