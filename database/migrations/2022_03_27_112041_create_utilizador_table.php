<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUtilizadorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('utilizador', function(Blueprint $table)
		{
			$table->unsignedInteger('id',true);
			$table->integer('numero')->unique('numero_UNIQUE');
			$table->string('email', 70)->nullable();
			$table->string('nome', 100);
			$table->string('login', 60)->nullable()->unique('login_UNIQUE');
			$table->smallInteger('tipo')->default(0);
			$table->integer('idCurso')->unsigned()->index('idCurso_idx');
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
		Schema::drop('utilizador');
	}

}
