<?php

namespace App\Http\Controllers;


use App\Models\Turno;
use App\Models\Anoletivo;
use App\Models\Inscricao;
use App\Models\Utilizador;
use App\Services\InscricaoService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\InscricaoResource;
use App\Http\Requests\InscricaoPostRequest;


class InscricaoController extends Controller
{
    public function store(InscricaoPostRequest $request){
        //fazer inscricao
        $idTurnosAceites = [];
        $turnosRejeitados = [];
        $idCadeiras = [];
        $idTurnosRemoved = ["removed" => [], "added" => []];

        $data = collect($request->validated());
        $canBeCreated = (new InscricaoService)->checkData($data);

        if($canBeCreated['response'] == 0){
            return response($canBeCreated['erro'], 401);
        }

        if ($canBeCreated['response'] == 2) {
            $idTurnosAceites = $canBeCreated['idTurnosAceites'];
            $turnosRejeitados = $canBeCreated['rejeitados'];
        } else if($canBeCreated['response'] == 1){
            $idTurnosAceites = $data->get('turnosIds');
        } 
        
        $inscricoesAtuais = Inscricao::join('turno', function ($join) {
            $join->on('turno.id', '=', 'idTurno')->where('idUtilizador', '=', Auth::user()->id)->where('numero', '>', 0);
        })->select('inscricao.id', 'turno.id as turnoId','turno.idCadeira','turno.tipo')->get();

        $turnosAceites = Turno::select('turno.*')->whereIn('turno.id', $idTurnosAceites)->get();

        //verificar se houve algum turno retirado, se foi entao apaga
        foreach ($inscricoesAtuais as $inscricaoAtual) {
            if (!in_array($inscricaoAtual->turnoId, $idTurnosAceites)) {
                $mudanca = 0;
                foreach ($turnosRejeitados as $key => $rejeitado) {
                    if($rejeitado->idCadeira == $inscricaoAtual->idCadeira && $rejeitado->tipo == $inscricaoAtual->tipo){
                        $mudanca = 1;
                        break;
                    }
                }
                if($mudanca == 0){
                    foreach ($turnosAceites as $key => $aceite) {
                        if($aceite->idCadeira == $inscricaoAtual->idCadeira && $aceite->tipo == $inscricaoAtual->tipo){
                            $mudanca = 1;
                            break;
                        }
                    }
                    if($mudanca == 0){
                        $inscricao = (new InscricaoService)->remove($inscricaoAtual->id, $inscricaoAtual->turnoId);
                        unset($idTurnosAceites[$inscricaoAtual->turnoId]);
                        array_push($idCadeiras,$inscricaoAtual->idCadeira);
                        array_push($idTurnosRemoved["removed"],$inscricaoAtual->turnoId);
                    }
                }    
            }else{
                unset($idTurnosAceites[array_search($inscricaoAtual->turnoId, $idTurnosAceites)]);
            }
        }

        $anoletivo = Anoletivo::where("ativo", 1)->first();
        $idsTurnos = DB::table('turno')->select('id','tipo','idCadeira')->whereIn('id', $idTurnosAceites)->get();
        foreach($idsTurnos as $turno){
            $subquery = "select i.*, t.tipo, t.idCadeira as cadeiraId from inscricao i join turno t on t.id = i.idTurno where i.idUtilizador = " . Auth::user()->id . " and t.tipo = '" . $turno->tipo . "' and t.idCadeira = '" . $turno->idCadeira . "' and t.idAnoletivo = " . $anoletivo->id;
            $inscricoes = DB::select(DB::raw($subquery));
            if (sizeof($inscricoes) == 0) {
                $inscricao = (new InscricaoService)->save(Auth::user()->id, $turno->id);
                if($inscricao != null){
                    array_push($idCadeiras,$turno->idCadeira);
                    array_push($idTurnosRemoved["added"],$turno->id);
                }
            }else{
                $inscricao = Inscricao::find($inscricoes[0]->id);
                if (!empty($inscricao)) {
                    $oldTurnoid = $inscricao->idTurno;
                    $inscricao = (new InscricaoService)->update($inscricao, $turno->id);
                    if($inscricao != null){
                        array_push($idCadeiras,$turno->idCadeira);
                        array_push($idTurnosRemoved["added"],$turno->id);
                        array_push($idTurnosRemoved["removed"],$oldTurnoid);
                    }
                }
            }            
        }
        
        //turnos para mostrar na pagina de inscricao, tem de ir assim formatados...
        $inscri = Inscricao::where('idUtilizador', ($request->user())->id)->join('turno','turno.id','=','inscricao.idTurno'
                            )->join('cadeira','turno.idCadeira','=','cadeira.id')
                            ->where('turno.idAnoletivo', '=', $anoletivo->id)->where('cadeira.semestre', $anoletivo->semestreativo)
                            ->select('turno.id', 'turno.tipo', 'turno.numero', 'cadeira.nome', 'cadeira.idCurso', 'turno.idCadeira as idCadeira', 'cadeira.ano')->get();
        $insToSend = [];
        foreach ($inscri as $key => $insc) {
            if(!array_key_exists($insc->idCurso,$insToSend)){
                $insToSend[$insc->idCurso] = [];
            }
            if(!array_key_exists($insc->idCadeira,$insToSend[$insc->idCurso])){
                $insToSend[$insc->idCurso][$insc->idCadeira] = ["nome" => $insc->nome, "ano" => $insc->ano, "turnos" => []];
            }
            array_push($insToSend[$insc->idCurso][$insc->idCadeira]["turnos"], $insc);
        }

        if ($canBeCreated['response'] == 2) {
            return response(["rejeitados" => $canBeCreated['rejeitados'], "idsCadeiras" => $idCadeiras, "updatedTurnos" => $idTurnosRemoved, "inscricoesTurnosAtuais" => $insToSend], 201);
        } else if($canBeCreated['response'] == 1){
            return response(["idsCadeiras" => $idCadeiras, "updatedTurnos" => $idTurnosRemoved, "inscricoesTurnosAtuais" => $insToSend],201);
        }

    }

    public function store2(InscricaoPostRequest $request){
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
        

    }

    public function delete(Inscricao $inscricao){
        $turnoId = $inscricao->idTurno;
        $del = $inscricao->delete();
        if(!$del){
            return response("Erro ao apagar a inscrição". 400);
        }
        Turno::where('id', $turnoId)->update(['vagasocupadas' => DB::raw('vagasocupadas-1')]);
        return response("Inscrição apagada com sucesso". 200);
    }
}
