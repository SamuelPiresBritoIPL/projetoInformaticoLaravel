<?php

namespace App\Services;
use App\Models\Turno;
use App\Models\Inscricao;
use Illuminate\Support\Facades\DB;

class InscricaoService
{

    public function save($idUtilizador, $idTurno){
        $inscricao = new Inscricao();
        $inscricao->idUtilizador = $idUtilizador;
        $inscricao->idTurno = $idTurno;

        $inscricao->save();
        return $inscricao;
    }

    public function update($inscricao, $turnoId){
        $inscricao->idTurno = $turnoId;

        $inscricao->save();
        
        return $inscricao;
    }

    public function checkData($data){
        $turnosutilizador = Turno::select('turno.*')->join('inscricaoucs', function ($join) use(&$data) {
            $join->on('turno.idCadeira', '=', 'inscricaoucs.idCadeira')->where('inscricaoucs.idUtilizador', '=', $data->get('idUtilizador'));
        })->get();
        if (empty($turnosutilizador)) {
            return ['response' => 0, 'erro' => 'Você não tem turnos disponíveis para se inscrever.'];
        } else {
            $idTurnosUtilizador = [];
            $turnosRejeitados = [];
            $idTurnosRequeridos = [];
            
            foreach ($turnosutilizador as $turno) {
                array_push($idTurnosUtilizador, $turno->id);
            }
            $idTurnosRequeridos = $data->get('turnosIds');
            
            foreach($data->get('turnosIds') as $turnoId){
                if(!in_array($turnoId, $idTurnosUtilizador)){
                    return ['response' => 0, 'erro' => 'Ocorreu um erro, dê refresh à página e tente novamente.'];
                } else {
                   foreach ($turnosutilizador as $turno) {
                       if ($turno->id == $turnoId) {
                           $vagasocupadas = Inscricao::where('idTurno', $turno->id)->count();
                           if ($turno->vagastotal == null or $turno->vagastotal <= $vagasocupadas) {
                                $turnoRejeitado = DB::table('turno')->select('turno.id', 'turno.tipo', 'turno.numero', 'cadeira.nome as cadeira', 'curso.nome as curso')
                                ->join('inscricaoucs', function ($join) use(&$data, &$turno) {
                                    $join->on('turno.idCadeira', '=', 'inscricaoucs.idCadeira')->where('inscricaoucs.idUtilizador', '=', $data->get('idUtilizador'))->where('turno.id', '=' , $turno->id);
                                })
                                ->join('cadeira', function ($join) {
                                    $join->on('turno.idCadeira', '=', 'cadeira.id')->select('cadeira.nome');
                                })
                                ->join('curso', function ($join) {
                                    $join->on('cadeira.idCurso', '=', 'curso.id')->select('curso.nome');
                                })->get();
                                array_push($turnosRejeitados, $turnoRejeitado[0]);
                                $idTurnosRequeridos = \array_filter($idTurnosRequeridos, static function ($element) use(&$turno) {
                                    return $element !== $turno->id;
                                });
                           } 
                       }
                   }
                }
            }
            if (sizeOf($turnosRejeitados) == sizeOf($data->get('turnosIds'))) {
                return ['response' => 0, 'erro' => 'Todos os turnos selecionados já se encontram com o total das vagas preenchido.'];
            }
            if (sizeOf($turnosRejeitados) > 0) {
                return ['response' => 2, 'rejeitados' => $turnosRejeitados , 'idTurnosAceites' =>  $idTurnosRequeridos];
            }
            if (sizeOf($turnosRejeitados) == 0) {
                return ['response' => 1];
            }

        }
    }
}