<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToUtilizadorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('utilizador', function(Blueprint $table)
		{
			$table->foreign('idCurso', 'utilizador_ibfk_1')->references('id')->on('curso')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('utilizador', function(Blueprint $table)
		{
			$table->dropForeign('utilizador_ibfk_1');
		});
	}

}
