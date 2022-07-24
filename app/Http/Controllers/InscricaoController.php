<?php

namespace App\Http\Controllers;


use App\Models\Aula;
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
        $anoletivo = Anoletivo::where("ativo", 1)->first();
        $inscricoesAtuais = Inscricao::join('turno', function ($join) {
            $join->on('turno.id', '=', 'idTurno')->where('idUtilizador', '=', Auth::user()->id)->where('numero', '>', 0);
        })->join('cadeira','turno.idCadeira','=','cadeira.id')->where('cadeira.semestre', $anoletivo->semestreativo)
        ->where('turno.idAnoletivo', $anoletivo->id)->select('inscricao.id', 'turno.id as turnoId','turno.idCadeira','turno.tipo')->get();

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

        //verificacao se existem turnos que coincidem
        $idTurnos = Inscricao::where('idUtilizador',Auth::user()->id)->
                                join('turno','turno.id','=','inscricao.idTurno')->where('turno.idAnoletivo', $anoletivo->id)
                                ->join('cadeira','turno.idCadeira','=','cadeira.id')->where('cadeira.semestre', $anoletivo->semestreativo)
                                ->pluck('inscricao.idTurno')->toArray();
        $coincidem = $this->getCoincidencias($idTurnos);
        $horariopessoal = $this->getHorarioPessoal($idTurnos);

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
            return response(["rejeitados" => $canBeCreated['rejeitados'], "idsCadeiras" => $idCadeiras, "updatedTurnos" => $idTurnosRemoved, "inscricoesTurnosAtuais" => $insToSend, "coicidem" => $coincidem, "horariopessoal" => $horariopessoal], 201);
        } else if($canBeCreated['response'] == 1){
            return response(["idsCadeiras" => $idCadeiras, "updatedTurnos" => $idTurnosRemoved, "inscricoesTurnosAtuais" => $insToSend, "coicidem" => $coincidem,"horariopessoal" => $horariopessoal],201);
        }

    }

    public function getCoincidencias($idTurnos){
        $aulas = Aula::whereIn('idTurno',$idTurnos)->whereNotNull('horaInicio')->orderby('data')->orderby('horaInicio')->get();
        $nrVezes = [];
        $coincidem = [];
        $week = [0 => "Domingo",1 => "Segunda-feira",2 => "Terca-feira",3 => "Quarta-feira",4 => "Quinta-feira",5 => "Sexta-feira",6 => "Sabado"];
        for($i = 0; $i < count($aulas)-1; $i++){
            if($aulas[$i]->data == $aulas[$i+1]->data && $aulas[$i]->horaFim >= $aulas[$i+1]->horaInicio){
                $pos = $aulas[$i]->idTurno.$aulas[$i+1]->idTurno;
                if(array_key_exists($aulas[$i+1]->idTurno.$aulas[$i]->idTurno,$nrVezes)){
                    $pos = $aulas[$i+1]->idTurno.$aulas[$i]->idTurno;
                }
                $weekNumber = date("W", strtotime($aulas[$i]->data));
                if(!array_key_exists($pos,$nrVezes)){
                    $nrVezes[$pos][0] = $weekNumber;
                    $nrVezes[$pos][1] = $aulas[$i];
                    $nrVezes[$pos][2] = $aulas[$i+1];
                    continue;
                }
                $nrVezes[$pos][0] .= " | ". $weekNumber;
            }
        }
        
        foreach ($nrVezes as $key => $aula) {
            $dayofweek = date('w', strtotime($aula[1]->data));
            array_push($coincidem, [0 =>  $aula[0], 1 => $week[$dayofweek], 2 => $aula[1]->turno->cadeira->nome . " (". $aula[1]->turno->tipo . ($aula[1]->turno->numero == 0 ? "" : $aula[1]->turno->numero) .")", 3 => $aula[2]->turno->cadeira->nome . " (". $aula[2]->turno->tipo . ($aula[2]->turno->numero == 0 ? "" : $aula[2]->turno->numero) .")"]);
        }
        return $coincidem;
    }

    public function checkCoincidencias(InscricaoPostRequest $request){
        $data = collect($request->validated());
        //ir buscar inscricoes de turnos unicos
        $anoletivo = Anoletivo::where("ativo", 1)->first();
        $inscricoesAtuaisTurnosUnicos = Inscricao::join('turno', function ($join) {
            $join->on('turno.id', '=', 'idTurno')->where('idUtilizador', '=', Auth::user()->id)->where('numero', 0);
        })->join('cadeira','turno.idCadeira','=','cadeira.id')->where('cadeira.semestre', $anoletivo->semestreativo)
        ->where('turno.idAnoletivo', $anoletivo->id)->pluck('idturno')->toArray();

        return response(["coicidem" => $this->getCoincidencias(array_merge($data["turnosIds"],$inscricoesAtuaisTurnosUnicos)),"horariopessoal" => $this->getHorarioPessoal(array_merge($data["turnosIds"],$inscricoesAtuaisTurnosUnicos))],200);
    }

    public function getHorarioPessoal($idTurnos){
        $query = DB::raw("(CASE WHEN numero='0' THEN '' ELSE numero END) as content");
        $aulas = Aula::whereIn('idTurno',$idTurnos)->join('turno','turno.id','=','aula.idTurno')->join('cadeira','turno.idCadeira','=','cadeira.id')
        ->whereNotNull('horaInicio')->orderby('data')->orderby('horaInicio')->orderby('horaInicio')
        ->select(DB::raw("CONCAT(aula.data,' ',horaInicio) AS start"),DB::raw("CONCAT(data,' ',horaFim) AS end"),"cadeira.nome as title",DB::raw("CONCAT(tipo,(CASE WHEN numero='0' THEN '' ELSE numero END)) as content"))->get();
        
        $data = Aula::whereIn('idTurno',$idTurnos)->join('turno','turno.id','=','aula.idTurno')->join('cadeira','turno.idCadeira','=','cadeira.id')
        ->whereNotNull('horaInicio')->orderby('data')->orderby('horaInicio')->orderby('horaInicio')
        ->select('data')->first();
        
        return ["data" => empty($data) ? null : $data->data, "horario"=> $aulas];
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
            return response("Erro ao apagar a inscrição", 400);
        }
        Turno::where('id', $turnoId)->update(['vagasocupadas' => DB::raw('vagasocupadas-1')]);
        return response("Inscrição apagada com sucesso", 200);
    }
}
