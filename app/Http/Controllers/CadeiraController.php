<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\Cadeira;
use App\Models\Anoletivo;
use App\Models\Utilizador;
use App\Models\Inscricaoucs;
use App\Services\CadeiraService;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CadeiraResource;
use App\Http\Requests\CadeiraPostRequest;
use App\Http\Resources\InscricaoucsResource;
use Illuminate\Http\Request;

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
            CadeiraResource::$format = 'inscricaoucsuser';
            return response(CadeiraResource::collection($dados),200);
        }
    }

    public function getInformacoesCadeira(Cadeira $cadeira, Anoletivo $anoletivo){
        $result = (new CadeiraService)->getInformacoesCadeirasForAdmin($cadeira, $anoletivo);

        return response($result["msg"],$result["code"]);
    }

    public function getCadeira(Cadeira $cadeira, Anoletivo $anoletivo){
        $tiposturnos = Turno::where('idCadeira', $cadeira->id)->select("tipo", DB::raw('count(*) as total'))->groupby("tipo")->pluck('tipo','total')->toArray();
        $totalinscritos = Inscricaoucs::where('idCadeira', $cadeira->id)->where('estado', 1)->where('idAnoletivo', $anoletivo->id)->select(DB::raw('count(*) as total'))->get();
        $numAlunos = $totalinscritos[0]->total;
        $data=[];
        foreach ($tiposturnos as $key => $value){
            array_push($data,["turno" => $value,"numeroturnos" => $key, "mediavagas" => round($numAlunos/$key)]);
        }
        
        $cadeiras = CadeiraResource::make($cadeira)->anoletivo($anoletivo->id,$cadeira->semestre);
        return response(["info" => $data, "cadeiras" => $cadeiras],200);
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

    public function editVagasTurnos(Request $request, Cadeira $cadeira, Anoletivo $anoletivo){
        if($request->has("tipoturno") && $request->has("vagas")){
            foreach($request->get("tipoturno") as $k => $turnotipo){
                if(array_key_exists($k,$request->get("vagas"))){
                    $turnos = Turno::where('idCadeira',$cadeira->id)->where('idAnoletivo', $anoletivo->id)->where('tipo',$turnotipo)->get();
                    foreach ($turnos as $key => $value) {
                        $value->vagastotal = $request->get("vagas")[$k];
                        $value->save();
                    }
                }
            }
        }else{
            return response("Faltam dados a serem enviados",401);
        }
        return response("valores alterados",200);
    }
}
