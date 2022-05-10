<?php

namespace App\Services;

use App\Http\Resources\PedidosResource;
use App\Models\Anoletivo;
use App\Models\Cadeira;
use App\Models\Pedidos;
use App\Models\Utilizador;

class EstudanteService
{
    public function getDadosEstudante(Utilizador $estudante, Anoletivo $anoletivo, $semestre){
        //buscar cadeiras aprovadas
        //cadeiras reprovadas
        //cadeira inscritas
        //pedidos confirmacao ucs reprovados, aprovados
        $cadeirasAprovadas = [];
        $cadeiras = Cadeira::leftjoin('inscricaoucs', function($join) use(&$estudante){
            $join->on('inscricaoucs.idCadeira','=','cadeira.id');
            $join->where('inscricaoucs.idUtilizador', '=', $estudante->id);
            $join->where('inscricaoucs.estado','=',2);
        })->leftjoin('curso', function($join){
            $join->on('curso.id','=','cadeira.idCurso');
        })->where('inscricaoucs.idUtilizador', '=', $estudante->id)->select('inscricaoucs.*','cadeira.*')->get();

        foreach ($cadeiras as $key => $cadeira) {
            if(!array_key_exists($cadeira->idCurso,$cadeirasAprovadas)){
                $cadeirasAprovadas[$cadeira->idCurso] = [];
            }
            array_push($cadeirasAprovadas[$cadeira->idCurso], $cadeira);
        }

        $cadeirasInscritas = [];
        $cadeiras = Cadeira::leftjoin('inscricaoucs', function($join) use(&$estudante){
            $join->on('inscricaoucs.idCadeira','=','cadeira.id');
            $join->where('inscricaoucs.idUtilizador', '=', $estudante->id);
            $join->where('inscricaoucs.estado','=',1);
        })->where('inscricaoucs.idUtilizador', '=', $estudante->id)->get();

        foreach ($cadeiras as $key => $cadeira) {
            if(!array_key_exists($cadeira->idCurso,$cadeirasInscritas)){
                $cadeirasInscritas[$cadeira->idCurso] = [];
            }
            array_push($cadeirasInscritas[$cadeira->idCurso], $cadeira);
        }

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

        $pedidos = PedidosResource::collection($estudante->pedidos);

        return ["msg" => ["todasCadeiras" => $todasCadeiras, "Pedidos" => $pedidos], "code" => 200];
    }
}