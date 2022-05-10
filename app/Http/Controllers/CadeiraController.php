<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Turno;
use App\Models\Cadeira;
use App\Models\Anoletivo;
use App\Models\Utilizador;
use App\Models\Coordenador;
use App\Models\Inscricaoucs;
use Illuminate\Http\Request;
use App\Services\CadeiraService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CadeiraResource;
use App\Http\Requests\CadeiraPostRequest;
use App\Http\Resources\InscricaoucsResource;
use App\Services\CoordenadorService;

class CadeiraController extends Controller
{

    public function getCadeirasUtilizador(Request $request){
        if($request->user()->tipo == 0){ //estudante
            InscricaoucsResource::$format = 'cadeiras';
            $anoletivo = Anoletivo::where('ativo',1)->first();
            $dados = Inscricaoucs::where('idUtilizador',($request->user())->id)->where('idAnoletivo',$anoletivo->id)
                            ->where('estado',1)->join('cadeira','inscricaoucs.idCadeira','=','cadeira.id')
                            ->where('cadeira.semestre', $anoletivo->semestreativo)->join('curso', 'curso.id','=','cadeira.idCurso')->get();
            $inscricaoucs = InscricaoucsResource::collection($dados);        

            $cursos = [];
            foreach ($inscricaoucs as $key => $inscricao) {
                if(!array_key_exists($inscricao->idCurso,$cursos)){
                    $cursos[$inscricao->idCurso] = [];
                }
                array_push($cursos[$inscricao->idCurso], $inscricao);
            }
            //dd($cursos);
            return response($cursos,200);
        }
    }

    public function getCadeirasProfessor(Anoletivo $anoletivo, $semestre){
        /*if(!(new CoordenadorService)->isProfessor($cadeira)){
            return response("Não tem permissão para aceder a esta unidade curricular",401);
        }*/
        $subquery = "(select count(*) from inscricao where idTurno = turno.id) as vagas";
        $turnos = Curso::join('cadeira', 'curso.id', '=', 'cadeira.idCurso')->join('turno','turno.idCadeira','=','cadeira.id')
            ->join('aula','aula.idTurno','=','turno.id')->where('turno.idAnoletivo', $anoletivo->id)->where('aula.idProfessor',Auth::user()->id)
            ->select('cadeira.*','turno.*','curso.nome as nomeCurso',DB::raw($subquery))->distinct('turno.id')->get();

        $dados = [];
        foreach ($turnos as $key => $turno) {
            if(!array_key_exists($turno->idCurso,$dados)){
                $dados[$turno->idCurso] = ["curso" => $turno->nomeCurso, "cadeiras" => []];
            }
            if(!array_key_exists($turno->idCadeira,$dados[$turno->idCurso]["cadeiras"])){
                $dados[$turno->idCurso]["cadeiras"][$turno->idCadeira] = [];
            }
            array_push($dados[$turno->idCurso]["cadeiras"][$turno->idCadeira], $turno);
        }
        return response($dados,200);
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
                        if(!empty($request->get("vagas")[$k])){
                            $value->vagastotal = $request->get("vagas")[$k];
                            $value->save();
                        }
                    }
                }
            }
        }else{
            return response("Faltam dados a serem enviados",401);
        }
        return response("valores alterados",200);
    }
}
