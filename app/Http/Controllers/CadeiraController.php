<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\Cadeira;
use App\Models\Utilizador;
use App\Services\CadeiraService;
use App\Http\Resources\CadeiraResource;
use App\Http\Requests\CadeiraPostRequest;
use App\Http\Resources\InscricaoucsResource;

class CadeiraController extends Controller
{
    public function getCadeirasUtilizador(Utilizador $utilizador){
        if($utilizador->tipo == 0){ //estudante
            InscricaoucsResource::$format = 'cadeiras';
            return response(InscricaoucsResource::collection($utilizador->inscricaoucs),200);
        }
    }

    public function getCadeirasNaoAprovadasUtilizador(Utilizador $utilizador){
        if($utilizador->tipo == 0){ //estudante
            InscricaoucsResource::$format = 'cadeiras';

            $dados = Cadeira::where('idCurso', $utilizador->idCurso)->leftJoin('inscricaoucs', function ($join) use(&$utilizador) {
                $join->on('cadeira.id', '=', 'inscricaoucs.idCadeira')
                     ->where('inscricaoucs.idUtilizador','=',$utilizador->id);
            })->select('inscricaoucs.*', 'cadeira.*' )->get();
            CadeiraResource::$format = 'inscricaoucs';
            return response(CadeiraResource::collection($dados),200);
        }
    }

    public function getInformacoesCadeira(Cadeira $cadeira){
        $result = (new CadeiraService)->getInformacoesCadeirasForAdmin($cadeira);

        return response($result["msg"],$result["code"]);
    }

    public function getCadeira(Cadeira $cadeira){
        return response(new CadeiraResource($cadeira),200);
    }

    public function addAluno(CadeiraPostRequest $request,Cadeira $cadeira){
        $data = collect($request->validated());

        $result = (new CadeiraService)->addStudentToUC($data,$cadeira);

        return response($result["msg"],$result["code"]);
    }

    public function addAlunoTurno(CadeiraPostRequest $request, Turno $turno){
        $data = collect($request->validated());

        $result = (new CadeiraService)->addStudentToTurno($data,$turno);

        return response($result["msg"],$result["code"]);
    }
}
