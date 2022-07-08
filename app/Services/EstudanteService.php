<?php

namespace App\Services;

use App\Models\Turno;
use App\Models\Cadeira;
use App\Models\Pedidos;
use App\Models\Anoletivo;
use App\Models\Inscricao;
use App\Models\Utilizador;
use App\Http\Resources\PedidosResource;
use App\Http\Controllers\InscricaoController;

class EstudanteService
{
    public function getDadosEstudante(Utilizador $estudante, Anoletivo $anoletivo, $semestre){
        //buscar cadeiras aprovadas
        //cadeira inscritas
        //pedidos confirmacao ucs reprovados, aprovados
        $cadeirasAprovadas = [];
        $cadeiras = Cadeira::leftjoin('inscricaoucs', function($join) use(&$estudante){
            $join->on('inscricaoucs.idCadeira','=','cadeira.id');
            $join->where('inscricaoucs.idUtilizador', '=', $estudante->id);
            $join->where('inscricaoucs.estado','=',2);
        })->leftjoin('curso', function($join){
            $join->on('curso.id','=','cadeira.idCurso');
        })->leftjoin('anoletivo', function($join){
            $join->on('anoletivo.id','=','inscricaoucs.idAnoletivo');
        })->where('inscricaoucs.idUtilizador', '=', $estudante->id)->select('inscricaoucs.*','cadeira.*', 'anoletivo.anoletivo')->get();

        foreach ($cadeiras as $key => $cadeira) {
            if(!array_key_exists($cadeira->idCurso,$cadeirasAprovadas)){
                $cadeirasAprovadas[$cadeira->idCurso] = ["nome" => $cadeira->nomeCurso, "cadeiras" => []];
            }
            array_push($cadeirasAprovadas[$cadeira->idCurso]["cadeiras"], $cadeira);
        }

        $cadeirasInscritas = [];
        $cadeiras = Cadeira::leftjoin('inscricaoucs', function($join) use(&$estudante){
            $join->on('inscricaoucs.idCadeira','=','cadeira.id');
            $join->where('inscricaoucs.idUtilizador', '=', $estudante->id);
            $join->where('inscricaoucs.estado','=',1);
        })->leftjoin('curso', function($join){
            $join->on('curso.id','=','cadeira.idCurso');
        })->where('inscricaoucs.idUtilizador', '=', $estudante->id)->select('inscricaoucs.*','cadeira.*','curso.nome as nomeCurso', 'curso.codigo as codigoCurso')->get();

        $turnos = Turno::join('inscricao', function($join){
            $join->on('turno.id','=','inscricao.idTurno');
        })->join('cadeira', function($join){
            $join->on('cadeira.id','=','turno.idCadeira');
        })->where('inscricao.idUtilizador', $estudante->id)->where('idAnoletivo', $anoletivo->id)->select('turno.*','cadeira.idCurso')->get();

        foreach ($cadeiras as $key => $cadeira) {
            if(!array_key_exists($cadeira->idCurso,$cadeirasInscritas)){
                $cadeirasInscritas[$cadeira->idCurso] = ["nome" => $cadeira->nomeCurso, "codigo" => $cadeira->codigoCurso, "cadeiras" => []];
            }
            array_push($cadeirasInscritas[$cadeira->idCurso]["cadeiras"], ["uc" => $cadeira, "turnos" => []]);
        }

        foreach ($turnos as $key => $turno) {
            if(array_key_exists($turno->idCurso,$cadeirasInscritas)){
                for ($i = 1; $i < sizeof($cadeirasInscritas[$turno->idCurso]["cadeiras"]); $i++) {
                    if($cadeirasInscritas[$turno->idCurso]["cadeiras"][$i]["uc"]->id == $turno->idCadeira){
                        array_push($cadeirasInscritas[$turno->idCurso]["cadeiras"][$i]["turnos"], $turno);
                    }
                }
            }
        }

        $infoAluno = Utilizador::where('id', $estudante->id)->select('login', 'nome')->get();


        /*
        $idsCursos = Cadeira::leftjoin('inscricaoucs', function($join){
            $join->on('inscricaoucs.idCadeira','=','cadeira.id');
        })->where('inscricaoucs.idUtilizador', '=', $estudante->id)->distinct('idCurso')->pluck('idCurso')->toArray();
        
        $todasCadeiras = [];
        foreach($idsCursos as $idCurso) {
            $cadeirsasCurso = Cadeira::where('idCurso', $idCurso)->leftjoin('inscricaoucs', function($join) use(&$estudante){
                $join->on('inscricaoucs.idCadeira','=','cadeira.id');
                $join->where('inscricaoucs.idUtilizador', '=', $estudante->id);
            })->orderBy('ano')->orderBy('semestre')->get();
            if(!array_key_exists($idCurso,$todasCadeiras)){
                $todasCadeiras[$idCurso] = [];
            }
            array_push($todasCadeiras[$idCurso], $cadeirsasCurso);
        }
        */

        $idTurnos = Inscricao::where('idUtilizador',$estudante->id)->
                                join('turno','turno.id','=','inscricao.idTurno')->where('turno.idAnoletivo', $anoletivo->id)
                                ->join('cadeira','turno.idCadeira','=','cadeira.id')->where('cadeira.semestre', $anoletivo->semestreativo)
                                ->pluck('inscricao.idTurno')->toArray();
        $horario = (new InscricaoController)->getHorarioPessoal($idTurnos);

        $pedidos = PedidosResource::collection($estudante->pedidos);

        return ["msg" => ["cadeirasAprovadas" => $cadeirasAprovadas, "cadeirasInscritas" => $cadeirasInscritas, "pedidos" => $pedidos, "aluno" => $infoAluno, 'horario' =>$horario], "code" => 200];
    }
}