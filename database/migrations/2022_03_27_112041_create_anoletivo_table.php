<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnoletivoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('anoletivo', function(Blueprint $table)
		{
			$table->unsignedInteger('id',true);
			$table->string('anoletivo', 10)->nullable();
			$table->smallInteger('semestreativo')->nullable();
			$table->smallInteger('ativo')->default(0);
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
		Schema::drop('anoletivo');
	}

}
