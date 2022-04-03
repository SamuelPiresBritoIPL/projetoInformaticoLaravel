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
			$table->unsignedInteger('id',true);
			$table->integer('idCadeira')->unsigned()->index('idCadeira_3_idx');
			$table->integer('idUtilizador')->unsigned()->index('idUtilizador_4_idx');
			$table->integer('idAnoletivo')->unsigned()->index('idAnoletivo_2_idx');
			$table->smallInteger('nrinscricoes')->default(1);
			$table->smallInteger('estado')->default(1);
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
		Schema::drop('inscricaoucs');
	}

}
