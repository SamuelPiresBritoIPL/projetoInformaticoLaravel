<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AulaSeeder extends Seeder
{
    private $numberOfAulas = 1;  

    private function newFakeAula($faker, $i)
    {
        $rand = rand(8, 21);
        $hour = $rand . ":00:00";
        $rand = $rand + 2;
        $hour2 = $rand . ":00:00";
        return [
                'diaSemana' =>  $faker->numberBetween(0, 5),
                'horaInicio' => $hour,
                'Horafim' => $hour2, 
                'idTurno' => $i];
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('pt_PT');
        for ($i = 2; $i <= 30; $i++) {
            $aula = $this->newFakeAula($faker, $i);
            DB::table('aula')->insert($aula);
            if($i%2 == 0){
                $aula = $this->newFakeAula($faker, $i);
                DB::table('aula')->insert($aula);
            }
        }
    }
}
