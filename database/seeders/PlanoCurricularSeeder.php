<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanoCurricularSeeder extends Seeder
{
    private $numberOfCursos = 10;  

    private function newFakeCurso($faker, $i)
    {
        $codes = [1, 2, 3, 4 , 5 , 6, 7 ,8 , 9, 10];

        return [
            'codigo' => $codes[$i-1],
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
        for ($i = 1; $i <= $this->numberOfCursos; $i++) {
            $planoCurricular = $this->newFakeCurso($faker, $i);
            DB::table('planocurricular')->insert($planoCurricular);
        }
    }
}
