<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class CoordenadorSeeder extends Seeder
{
    private $numberOfCoordenador = 4;  

    private function newFakeCoordenador($faker, $i)
    {
        return [
            'idUtilizador' => $faker->numberBetween(1, 99),
            'tipo' => 0,
            'idCurso' => $faker->numberBetween(1, 5), 
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
        for ($i = 1; $i <= $this->numberOfCoordenador; $i++) {
            $planoCurricular = $this->newFakeCoordenador($faker, $i);
            DB::table('coordenador')->insert($planoCurricular);
        }
    }
}
