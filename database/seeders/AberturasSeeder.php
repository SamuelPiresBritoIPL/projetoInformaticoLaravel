<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AberturasSeeder extends Seeder
{
    private $numberOfAberturas = 3;  

    private function newFakeUtilizador($faker)
    {
        $openDate = $faker->dateTimeBetween('+ 5 days', '+30 days');
        $finishDate = $openDate;
        while($openDate <= $finishDate){
            $finishDate = $faker->dateTimeBetween('+ 6 days', '+40 days');
        }
        $year = $faker->numberBetween(1, 3);
        $openType = $faker->numberBetween(0, 1);
        $idUtilizador = $faker->numberBetween(20, 70);
        $idCurso = $faker->numberBetween(1, 5);

        return [
            'dataAbertura' => $openDate,
            'dataEncerar' => $finishDate,
            'ano' => $year,
            'tipoAbertura' => $openType,
            'idUtilizador' => $idUtilizador,
            'idCurso' => $idCurso
        ];
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('pt_PT');
        for ($i = 1; $i <= $this->numberOfAberturas; $i++) {
            $abertura = $this->newFakeUtilizador($faker);
            DB::table('aberturas')->insert($abertura);
        }
    }
}
