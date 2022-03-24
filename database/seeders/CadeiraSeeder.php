<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CadeiraSeeder extends Seeder
{
    private $numberOfCadeiras = 10;  

    private function newFakeCadeira($faker, $i)
    {
        $names = ["Análise Matemática", "Álgebra Linear", "Apoio à Gestão", "Sistemas de Base de Dados", "Matemática Discreta", "Estatística", "Contabilida", "Física Aplicada", "Análise de Dados", "Inovação e Empreendedorismo"];
        $abreviations = ["AM", "AL", "AG", "SBD", "MD", "EST", "CT", "FA", "AD", "IE"];

        $indice = $faker->numberBetween(0, 9);
        return [
                'nome' => $names[$indice],
                'abreviatura' => $abreviations[$indice],
                'idPlanoCurricular' => $faker->numberBetween(1, 4), 
                'codigo' => $i];
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('pt_PT');
        for ($i = 1; $i <= $this->numberOfCadeiras; $i++) {
            $cadeira = $this->newFakeCadeira($faker, $i);
            DB::table('cadeira')->insert($cadeira);
            $faker2 = $cadeira["idPlanoCurricular"];
            while ($cadeira["idPlanoCurricular"] == $faker2){
                $faker2 = $faker->numberBetween(1, 4);
            }
            $cadeira["idPlanoCurricular"] = $faker2;
            DB::table('cadeira')->insert($cadeira);
        }
    }
}
