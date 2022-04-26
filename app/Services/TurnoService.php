<?php

namespace App\Services;

use App\Http\Resources\TurnoResource;
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
                "totalnaorepetentes" => $totalNaorepetentes,"turno"=> new TurnoResource($turno), "alunos" => $alunos];
        return ['msg' => $send,'code' => 200];
    }

    public function editTurno($data,$turno){
        if($data->has('visivel')){
            $turno->visivel = $data->get('visivel');
        }
        if($data->has('repetentes')){
            $turno->repetentes = $data->get('repetentes');
        }
        if($data->has('vagastotal')){
            $turno->vagastotal = $data->get('vagastotal');
        }
        $turno->save();
        return ['msg' => "Alterações feitas com sucesso",'code' => 200];
    }
}