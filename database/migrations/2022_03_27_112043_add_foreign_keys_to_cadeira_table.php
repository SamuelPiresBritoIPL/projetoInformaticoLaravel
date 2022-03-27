<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCadeiraTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cadeira', function(Blueprint $table)
		{
			$table->foreign('idCurso', 'idPlanoCurricular')->references('id')->on('curso')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cadeira', function(Blueprint $table)
		{
			$table->dropForeign('idPlanoCurricular');
		});
	}

}
