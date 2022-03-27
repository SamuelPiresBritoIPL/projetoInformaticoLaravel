<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoordenadorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('coordenador', function(Blueprint $table)
		{
			$table->unsignedInteger('id',true);
			$table->integer('idUtilizador')->unsigned()->index('idUtilizador_idx');
			$table->string('tipo', 45)->default('0');
			$table->integer('idCurso')->unsigned()->index('idCurso_idx');
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
		Schema::drop('coordenador');
	}

}
