<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidosucsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pedidosucs', function(Blueprint $table)
		{
			$table->integer('id')->primary();
			$table->integer('idCadeira')->index('idCadeira___idx');
			$table->integer('idPedidos')->index('idPedidos_idx');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pedidosucs');
	}

}
