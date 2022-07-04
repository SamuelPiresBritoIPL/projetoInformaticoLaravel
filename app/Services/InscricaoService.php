<?php

namespace App\Services;
use App\Models\Turno;
use App\Models\Anoletivo;
use App\Models\Inscricao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InscricaoService
{

    public function save($idUtilizador, $idTurno){
        $inscricao = null;
        DB::transaction(function () use (&$idUtilizador, &$idTurno, &$inscricao) {
            $turno = Turno::where('id', $idTurno)->first();
            if($turno->vagastotal > $turno->vagasocupadas){
                Turno::where('id', $idTurno)->update(['vagasocupadas' => DB::raw('vagasocupadas+1')]);
            }else{
                return null; // pensar no que dar return!
            }
            $inscricao = new Inscricao();
            $inscricao->idUtilizador = $idUtilizador;
            $inscricao->idTurno = $idTurno;
            $inscricao->save();
        }, 5);
        return $inscricao;
    }

    public function update($inscricao, $turnoId){
        DB::transaction(function () use (&$inscricao, &$turnoId) {
            $turno = Turno::where('id', $turnoId)->first();
            if($turno->vagastotal > $turno->vagasocupadas){
                Turno::where('id', $turnoId)->update(['vagasocupadas' => DB::raw('vagasocupadas+1')]);
            }else{
                return null; // pensar no que dar return!
            }
            Turno::where('id', $inscricao->idTurno)->update(['vagasocupadas' => DB::raw('vagasocupadas-1')]);
            $inscricao->idTurno = $turnoId;
            $inscricao->save();
        }, 5);
        return $inscricao;
    }

    public function remove($inscricaoId, $turnoId){
        DB::transaction(function () use ($inscricaoId, $turnoId) {
            Turno::where('id', $turnoId)->update(['vagasocupadas' => DB::raw('vagasocupadas-1')]);
            Inscricao::where('id',$inscricaoId)->delete();
        }, 5);
    }

    public function checkData($data){
        $anoletivo = Anoletivo::where("ativo", 1)->first();
        $idTurnosUtilizador = Turno::select('turno.*')->join('inscricaoucs', function ($join)use(&$anoletivo) {
            $join->on('turno.idCadeira', '=', 'inscricaoucs.idCadeira')->where('inscricaoucs.idUtilizador', '=', Auth::user()->id);
        })->join('cadeira','turno.idCadeira','=','cadeira.id')->where('cadeira.semestre', $anoletivo->semestreativo)
        ->where('turno.idAnoletivo', $anoletivo->id)->pluck('turno.id')->toArray();
        if (empty($idTurnosUtilizador)) {
            return ['response' => 0, 'erro' => 'Você não tem turnos disponíveis para se inscrever.'];
        } else {
            $turnosRejeitados = [];
            $idTurnosRequeridos = [];
            $idTurnoUser = [];
            
            $idTurnosRequeridos = $data->get('turnosIds') ;
            $idTurnoUser = Turno::select('turno.*')->join('inscricao', function ($join) {
                $join->on('turno.id', '=', 'inscricao.idTurno')->where('inscricao.idUtilizador', '=', Auth::user()->id);
            })->join('cadeira','turno.idCadeira','=','cadeira.id')->where('cadeira.semestre', $anoletivo->semestreativo)
            ->where('turno.idAnoletivo', $anoletivo->id)
            ->whereIn('turno.id', $idTurnosRequeridos)->pluck('id')->toArray();
            
            $idTurnosRequeridos = array_diff($idTurnosRequeridos,$idTurnoUser);

            $turnos = Turno::whereIn('id', $idTurnosRequeridos)->get();
            foreach($turnos as $turno){
                if(!in_array($turno->id, $idTurnosUtilizador)){
                    return ['response' => 0, 'erro' => 'Ocorreu um erro, dê refresh à página e tente novamente.'];
                } else {
                    //$vagasocupadas = Inscricao::where('idTurno', $turno->id)->count();
                    $vagasocupadas = $turno->vagasocupadas;
                    if ($turno->vagastotal == null or $turno->vagastotal <= $vagasocupadas) {
                        $turnoRejeitado = DB::table('turno')->select('turno.id', 'turno.tipo', 'turno.numero', 'cadeira.nome as cadeira', 'curso.nome as curso','turno.idCadeira','turno.tipo')
                        ->join('inscricaoucs', function ($join) use(&$data, &$turno) {
                            $join->on('turno.idCadeira', '=', 'inscricaoucs.idCadeira')->where('inscricaoucs.idUtilizador', '=', Auth::user()->id)->where('turno.id', '=' , $turno->id);
                        })
                        ->join('cadeira', function ($join) {
                            $join->on('turno.idCadeira', '=', 'cadeira.id')->select('cadeira.nome');
                        })
                        ->join('curso', function ($join) {
                            $join->on('cadeira.idCurso', '=', 'curso.id')->select('curso.nome');
                        })->first();
                        array_push($turnosRejeitados, $turnoRejeitado);
                        $idTurnosRequeridos = \array_filter($idTurnosRequeridos, static function ($element) use(&$turno) {
                            return $element !== $turno->id;
                        });
                    } 
                }
            }
            if (!empty($data->get('turnosIds')) && sizeOf($turnosRejeitados) == sizeOf($data->get('turnosIds'))) {
                return ['response' => 0, 'erro' => 'Todos os turnos selecionados já se encontram com o total das vagas preenchido.'];
            }
            if (sizeOf($turnosRejeitados) > 0) {
                return ['response' => 2, 'rejeitados' => $turnosRejeitados , 'idTurnosAceites' =>  array_merge($idTurnosRequeridos,$idTurnoUser)];
            }
            if (sizeOf($turnosRejeitados) == 0) {
                return ['response' => 1];
            }

        }
    }
}