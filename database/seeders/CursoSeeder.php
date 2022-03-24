<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CursoSeeder extends Seeder
{
    private $numberOfCursos = 5;  

    private function newFakeCurso($i)
    {
        $names = ["Engenharia Informática", "Engenharia Mecância", "Engenharia Eletromecânica", "Gestão", "Contabilidade e Finanças"];
        $abreviations = ["EI", "EM", "EE", "G", "CF"];

        return [
            'nome' => $names[$i-1],
            'codigo' => $i, 
            'abreviatura' => $abreviations[$i-1]
        ];

    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= $this->numberOfCursos; $i++) {
            $curso = $this->newFakeCurso($i);
            DB::table('curso')->insert($curso);
        }
    }
}
