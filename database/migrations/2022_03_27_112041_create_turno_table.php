<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTurnoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('turno', function(Blueprint $table)
		{
			$table->unsignedInteger('id',true);
			$table->integer('idCadeira')->unsigned()->index('idCadeira_idx');
			$table->integer('idAnoletivo')->unsigned()->index('idAnoletivo_idx');
			$table->integer('vagastotal')->nullable();
			$table->integer('vagasocupadas')->unsigned()->default(0);
			$table->smallInteger('repetentes')->default(1);
			$table->smallInteger('visivel')->default(1);
			$table->string('tipo', 4);
			$table->smallInteger('numero')->default(0);
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
		Schema::drop('turno');
	}

}
