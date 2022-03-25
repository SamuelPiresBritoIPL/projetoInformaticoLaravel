<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanoCurricularSeeder extends Seeder
{
    private $numberOfPlanos = 4;  

    private function newFakeCurso($faker, $i)
    {
        $anos = ["2015/2017","2017/2018","2019/2020","2014/2015"];

        return [
            'ano' => $anos[$i-1],
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
        for ($i = 1; $i <= $this->numberOfPlanos; $i++) {
            $planoCurricular = $this->newFakeCurso($faker, $i);
            DB::table('planocurricular')->insert($planoCurricular);
        }
    }
}
