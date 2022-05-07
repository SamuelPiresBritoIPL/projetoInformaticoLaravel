<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pedidos', function(Blueprint $table)
		{
			$table->unsignedInteger('id',true);
			$table->integer('idUtilizador')->unsigned()->index('idUtilizado_idx');
			$table->integer('idAnoletivo')->unsigned()->index('idAnoletivo_idx');
			$table->integer('idCurso')->unsigned()->index('idCurso8_idx');
			$table->smallInteger('semestre')->default(1);
			$table->smallInteger('estado')->default(0);
			$table->string('descricao', 200)->nullable();
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
		Schema::drop('pedidos');
	}

}
