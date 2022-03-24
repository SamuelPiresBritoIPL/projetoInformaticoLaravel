<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CadeiraSeeder extends Seeder
{
    private $numberOfCursos = 10;  

    private function newFakeCurso($faker, $i)
    {
        $names = ["Análise Matemática", "Álgebra Linear", "Apoio à Gestão", "Sistemas de Base de Dados", "Matemática Discreta", "Estatística", "Contabilida", "Física Aplicada", "Análise de Dados", "Inovação e Empreendedorismo"];
        $abreviations = ["AM", "AL", "AG", "SBD", "MD", "EST", "CT", "FA", "AD", "IE"];

        $indice = $faker->numberBetween(0, 9);
        $i = 1;
        return [
            'nome' => $names[$indice],
            'abreviatura' => $abreviations[$indice],
            'idCurso' => $faker->numberBetween(1, 5), 
            'codigo' => $i,
        ];
        $i++;
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
