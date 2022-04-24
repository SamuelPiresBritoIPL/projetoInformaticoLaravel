<?php

namespace App\Services;

use App\Models\Anoletivo;
use App\Models\Inscricaoucs;
use Illuminate\Support\Facades\DB;

class TurnoService
{
    public function getInformacoesTurnoForAdmin($turno){
        $anoletivo = Anoletivo::where("ativo", 1)->first();
        if(empty($anoletivo)){
            return ['msg' => "Error",'code' => 404];
        }
        $alunos = $turno->inscricaosutilizadores;
        $totalNaorepetentes = 0;
        $totalRepetentes = 0;
        foreach ($alunos as $aluno) {
            if($aluno->nrinscricoes == 1){
                $totalNaorepetentes++;
            }else{
                $totalRepetentes++;
            }
        }
        $send = ["totalinscritos" => $totalNaorepetentes+$totalRepetentes,"totalrepetentes" => $totalRepetentes,
                "totalnaorepetentes" => $totalNaorepetentes, "alunos" => $alunos];
        return ['msg' => $send,'code' => 200];
    }
}