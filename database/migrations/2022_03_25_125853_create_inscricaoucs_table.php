<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscricaoucsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('inscricaoucs', function(Blueprint $table)
		{
			$table->integer('id')->unsigned()->primary();
			$table->integer('idCadeira')->unsigned()->index('idCadeira_3_idx');
			$table->integer('idUtilizador')->unsigned()->index('idUtilizador_4_idx');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('inscricaoucs');
	}

}
