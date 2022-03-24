<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnoSeeder extends Seeder
{
    private $numberOfCoordenador = 4;  

    private function newFakeCoordenador($faker, $i)
    {
        $turnos = ["TP1", "TP2", "TP3", "TP4", "TP5"];
        $j = 0;
        $luck = 0;
        while($j < sizeof($turnos) && $luck == 0){
            $number = $faker->numberBetween(1, 5);
            $turno = [
                'nome' => $turnos[$j],
                'idCadeira' => $i
            ];
            DB::table('turno')->insert($turno);
            $j++;
            $luck = $faker->numberBetween(0, 1);
        }
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('pt_PT');
        for ($i = 1; $i <= 20; $i++) {
            $this->newFakeCoordenador($faker, $i);
        }
    }
}
