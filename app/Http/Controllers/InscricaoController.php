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

        foreach($idTurnosAceites as $turnoId){
            $tipoturno = DB::table('turno')->select('tipo')->where('id', $turnoId)->get();
            if (!empty($tipoturno)) {
                $subquery = "select i.*, t.tipo, t.idCadeira,c.id as cadeiraId from inscricao i join turno t on t.id = i.idTurno join cadeira c on c.id = t.idCadeira where idUtilizador = " . $data->get('idUtilizador') . " and t.tipo = '" . $tipoturno[0]->tipo . "'";
                $inscricoes = DB::select(DB::raw($subquery));
     
                if (sizeof($inscricoes) > 1) {
                    $idCadeiraTurnoRequerido = DB::table('turno')->select('idCadeira')->where('id', $turnoId)->get();
                    foreach ($inscricoes as $inscricao) {
                        if ($idCadeiraTurnoRequerido[0]->idCadeira == $inscricao->idCadeira) {
                            $inscricao = Inscricao::find($inscricao->id);
                            if (!empty($inscricao)) {
                                $inscricao = (new InscricaoService)->update($inscricao, $turnoId);
                            }
                        }
                    }
                } else if ((sizeof($inscricoes) == 1 and $inscricoes[0]->idTurno == $turnoId)) {
                    //não faz nada pois ja esta inscrito
                } else {
                    if(!empty($inscricoes)){
                        $inscricao = Inscricao::find($inscricoes[0]->id);
                        if (!empty($inscricao)) {
                            $inscricao = (new InscricaoService)->save($data->get('idUtilizador'), $turnoId);
                        }
                    }   
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
}
