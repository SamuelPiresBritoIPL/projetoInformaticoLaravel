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
			$table->unsignedInteger('idUtilizador')->index('idUtilizadorr_idx');
			$table->unsignedInteger('idTurno')->index('idTurnoo_idx');
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
