<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscricaoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('inscricao', function(Blueprint $table)
		{
			$table->unsignedInteger('id',true);
			$table->integer('idUtilizador')->unsigned()->index('idUtilizadorr_idx');
			$table->integer('idTurno')->unsigned()->index('idTurnoo_idx');
			$table->timestamps();
			$table->softDeletes();
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
		Schema::drop('inscricao');
	}

}
