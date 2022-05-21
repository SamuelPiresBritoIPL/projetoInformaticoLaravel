<?php

namespace App\Http\Controllers;


use App\Models\Utilizador;
use App\Models\Inscricao;
use App\Models\Turno;
use App\Http\Requests\InscricaoPostRequest;
use App\Http\Resources\InscricaoResource;
use App\Services\InscricaoService;
use Illuminate\Support\Facades\DB;


class InscricaoController extends Controller
{
    public function store(InscricaoPostRequest $request){
        //fazer inscricao
        $idTurnosAceites = [];

        $data = collect($request->validated());
        $canBeCreated = (new InscricaoService)->checkData($data);

        if($canBeCreated['response'] == 0){
            return response($canBeCreated['erro'], 401);
        }

        if ($canBeCreated['response'] == 2) {
            $idTurnosAceites = $canBeCreated['idTurnosAceites'];
        } else if($canBeCreated['response'] == 1){
            $idTurnosAceites = $data->get('turnosIds');
        } 

        $inscricoesAtuais = Inscricao::join('turno', function ($join) use(&$data) {
            $join->on('turno.id', '=', 'idTurno')->where('idUtilizador', '=', $data->get('idUtilizador'));
        })->select('inscricao.id', 'turno.id as turnoId')->get();

        //verificar se houve algum turno retirado, se foi entao apaga
        foreach ($inscricoesAtuais as $inscricaoAtual) {
            if (!in_array($inscricaoAtual->turnoId, $idTurnosAceites)) {
                $inscricao = Inscricao::find($inscricaoAtual->id);
                $inscricao->delete();
            }
        }

        foreach($idTurnosAceites as $turnoId){
            $tipoturno = DB::table('turno')->select('tipo')->where('id', $turnoId)->get();
            if (!empty($tipoturno)) {
                $subquery = "select i.*, t.tipo, t.idCadeira,c.id as cadeiraId from inscricao i join turno t on t.id = i.idTurno join cadeira c on c.id = t.idCadeira where i.idUtilizador = " . $data->get('idUtilizador') . " and t.tipo = '" . $tipoturno[0]->tipo . "'";
                $inscricoes = DB::select(DB::raw($subquery));

                if (sizeof($inscricoes) > 0) {
                    $idCadeiraTurnoRequerido = DB::table('turno')->select('idCadeira')->where('id', $turnoId)->get();
                    $inscricaoNova = true;
                    $idInscricao = 0;
                    foreach ($inscricoes as $inscricao) {
                        if ($idCadeiraTurnoRequerido[0]->idCadeira == $inscricao->idCadeira) {
                            $inscricaoNova = false;
                            $idInscricao = $inscricao->id;
                            break;
                        }
                    }
                    if ($inscricaoNova == true) {
                        $inscricao = (new InscricaoService)->save($data->get('idUtilizador'), $turnoId);
                    } 
                    if ($inscricaoNova == false) {
                        $inscricao = Inscricao::find($idInscricao);
                        if (!empty($inscricao)) {
                            $inscricao = (new InscricaoService)->update($inscricao, $turnoId);
                        }
                    }
                }

                if (sizeof($inscricoes) == 0) {
                    $inscricao = (new InscricaoService)->save($data->get('idUtilizador'), $turnoId);
                }
            } else {
                return response('Ocorreu um erro, dê refresh à página e tente novamente.', 401);
            }   
        }

        if ($canBeCreated['response'] == 2) {
            return response($canBeCreated['rejeitados'], 201);
        } else if($canBeCreated['response'] == 1){
            return response(201);
        }

    }

    public function delete(Inscricao $inscricao){
        $del = $inscricao->delete();
        if(!$del){
            return response("Erro ao apagar a inscrição". 400);
        }
        return response("Inscrição apagada com sucesso". 200);
    }
}
