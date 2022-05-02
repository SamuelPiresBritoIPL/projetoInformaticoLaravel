<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\Cadeira;
use App\Models\Utilizador;
use App\Services\CadeiraService;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CadeiraResource;
use App\Http\Requests\CadeiraPostRequest;
use App\Http\Resources\InscricaoucsResource;
use App\Models\Inscricaoucs;

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
        $tiposturnos = Turno::where('idCadeira', $cadeira->id)->select("tipo", DB::raw('count(*) as total'))->groupby("tipo")->get();
        $totalinscritos = Inscricaoucs::where('idCadeira', $cadeira->id)->where('estado', 1)->where('idAnoletivo', 1)->select(DB::raw('count(*) as total'))->get();
        //dd($totalinscritos);
        $cadeiras = new CadeiraResource($cadeira);
        //return response(["info" => "tes", "cadeiras" => $cadeiras],200);
        return response($cadeiras,200);
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
