<?php

namespace App\Services;

use App\Models\Turno;
use App\Models\Cadeira;
use App\Models\Pedidos;
use App\Models\Anoletivo;
use App\Models\Utilizador;
use App\Http\Resources\PedidosResource;

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
        })->where('inscricaoucs.idUtilizador', '=', $estudante->id)->select('inscricaoucs.*','cadeira.*','curso.nome as nomeCurso')->get();

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
        })->where('inscricaoucs.idUtilizador', '=', $estudante->id)->select('inscricaoucs.*','cadeira.*','curso.nome as nomeCurso')->get();

        $turnos = Turno::join('inscricao', function($join){
            $join->on('turno.id','=','inscricao.idTurno');
        })->join('cadeira', function($join){
            $join->on('cadeira.id','=','turno.idCadeira');
        })->where('inscricao.idUtilizador', $estudante->id)->where('idAnoletivo', $anoletivo->id)->select('turno.*','cadeira.idCurso')->get();

        foreach ($cadeiras as $key => $cadeira) {
            if(!array_key_exists($cadeira->idCurso,$cadeirasInscritas)){
                $cadeirasInscritas[$cadeira->idCurso] = ["nome" => $cadeira->nomeCurso, "cadeiras" => []];
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

        $pedidos = PedidosResource::collection($estudante->pedidos);

        return ["msg" => ["cadeirasAprovadas" => $cadeirasAprovadas, "cadeirasInscritas" => $cadeirasInscritas, "pedidos" => $pedidos], "code" => 200];
    }
}