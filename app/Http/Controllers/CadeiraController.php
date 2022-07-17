<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Aula;
use App\Models\Curso;
use App\Models\Turno;
use App\Models\Cadeira;
use App\Models\Aberturas;
use App\Models\Anoletivo;
use App\Models\Inscricao;
use App\Models\Utilizador;
use App\Models\Coordenador;
use App\Models\Inscricaoucs;
use Illuminate\Http\Request;
use App\Services\CadeiraService;
use PhpParser\Node\Stmt\Foreach_;
use Illuminate\Support\Facades\DB;
use App\Services\CoordenadorService;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CadeiraResource;
use App\Http\Resources\PedidosResource;
use App\Http\Requests\CadeiraPostRequest;
use Doctrine\DBAL\Schema\Visitor\Visitor;
use App\Http\Resources\InscricaoucsResource;
use App\Http\Controllers\InscricaoController;

class CadeiraController extends Controller
{
    public function mudarTurno(CadeiraPostRequest $request, Turno $turno){
        $data = collect($request->validated());
        $ids = [];
        $turnosIds = Inscricao::whereIn('id',$data->get('inscricaoIds'))->select('idturno')->pluck('idturno')->toArray();
        foreach ($turnosIds as $key => $value) {
            
            if(!array_key_exists($value,$ids)){
                $ids[$value] = 0;
            }
            $ids[$value] += 1;
        }
        $inscricao = Inscricao::whereIn('id',$data->get('inscricaoIds'))->update(['idTurno' => $turno->id]);
        foreach ($ids as $key => $value) {
            Turno::where('id', $key)->update(['vagasocupadas' => DB::raw('vagasocupadas-'. $value)]);
        }
        Turno::where('id', $turno->id)->update(['vagasocupadas' => DB::raw('vagasocupadas+'.count($data->get('inscricaoIds')))]);
        return response("dados alterados com sucesso", 200);
    }

    public function exportCadeira(Cadeira $cadeira){
        $result = (new CadeiraService)->exportCadeira($cadeira);

        return response($result["msg"],$result["code"]);
    }

    public function getCadeirasUtilizador(Request $request){
        if($request->user()->tipo == 0){ //estudante
            $anoletivo = Anoletivo::where('ativo',1)->first();
            $cadeiras = Inscricaoucs::where('inscricaoucs.idUtilizador',($request->user())->id)->where('inscricaoucs.idAnoletivo',$anoletivo->id)
                            ->where('estado',1)->join('cadeira','inscricaoucs.idCadeira','=','cadeira.id')
                            ->where('cadeira.semestre', $anoletivo->semestreativo)->join('curso', 'curso.id','=','cadeira.idCurso')
                            ->select('inscricaoucs.*','cadeira.*')->get();
            $now = Carbon::now('Europe/Lisbon');
            $aberturas = Aberturas::where('aberturas.dataAbertura', '<=', $now)->where('aberturas.dataEncerar', '>=', $now)
                                ->whereNull('deleted_at')->whereIn('idCurso', function($query) use(&$request,&$anoletivo){
                                    $query->from('inscricaoucs')
                                          ->where('inscricaoucs.idUtilizador',($request->user())->id)->where('inscricaoucs.idAnoletivo',$anoletivo->id)
                                          ->where('estado',1)->join('cadeira','inscricaoucs.idCadeira','=','cadeira.id')
                                          ->where('cadeira.semestre', $anoletivo->semestreativo)->join('curso', 'curso.id','=','cadeira.idCurso')
                                          ->select('curso.id')->distinct('curso.id')->pluck('curso.id')->toArray();
                                })->where('tipoAbertura',1)->get();
            $cadeirasEnviar = [];
            foreach ($aberturas as $key => $abertura) {
                foreach ($cadeiras as $key => $cadeira) {
                    if($abertura->ano == 0 && $cadeira->idCurso == $abertura->idCurso){
                        array_push($cadeirasEnviar, $cadeira);
                    }else{
                        if($abertura->ano == $cadeira->ano && $cadeira->idCurso == $abertura->idCurso){
                            array_push($cadeirasEnviar, $cadeira);
                        }
                    }
                }
            }
            InscricaoucsResource::$format = 'cadeiras';
            $inscricaoucs = InscricaoucsResource::collection($cadeirasEnviar);
            $ins = Inscricao::where('idUtilizador', ($request->user())->id)->join('turno','turno.id','=','inscricao.idTurno'
                                    )->join('cadeira','turno.idCadeira','=','cadeira.id')
                                    ->where('turno.idAnoletivo', '=', $anoletivo->id)->where('cadeira.semestre', $anoletivo->semestreativo)
                                    ->where('turno.numero', '>', 0)->select('turno.id', 'turno.tipo', 'turno.numero', 'cadeira.nome', 'cadeira.idCurso', 'turno.idCadeira as idCadeira', 'cadeira.ano')->get();
            $cursos = [];
            foreach ($inscricaoucs as $key => $inscricao) {
                if(!array_key_exists($inscricao->idCurso,$cursos)){
                    $cursos[$inscricao->idCurso] = [];
                }
                array_push($cursos[$inscricao->idCurso], $inscricao);
            }

            $aberturaAtivas = Aberturas::where('aberturas.dataEncerar', '>=', $now)
            ->whereNull('deleted_at')->whereIn('idCurso', function($query) use(&$request,&$anoletivo){
                $query->from('inscricaoucs')
                      ->where('inscricaoucs.idUtilizador',($request->user())->id)->where('inscricaoucs.idAnoletivo',$anoletivo->id)
                      ->where('estado',1)->join('cadeira','inscricaoucs.idCadeira','=','cadeira.id')
                      ->where('cadeira.semestre', $anoletivo->semestreativo)->join('curso', 'curso.id','=','cadeira.idCurso')
                      ->select('curso.id')->distinct('curso.id')->pluck('curso.id')->toArray();
            })->where('tipoAbertura',1)->join('curso','idCurso','=','curso.id')->select('curso.id as idCurso', 'curso.nome', 'curso.codigo', 'dataAbertura', 'dataEncerar', 'ano')->get();

            $aberturasPorCurso = [];
            foreach ($aberturaAtivas as $key => $aberturaAtiva) {
                if(!array_key_exists($aberturaAtiva->idCurso, $aberturasPorCurso)){
                    $aberturasPorCurso[$aberturaAtiva->idCurso] = [];
                }
                $dataAbertura = Carbon::parse($aberturaAtiva->dataAbertura);
                $dataEncerrar = Carbon::parse($aberturaAtiva->dataEncerar);
                $now = Carbon::now('Europe/Lisbon');
                $dias = $dataAbertura->diffInDays($now);
                $diasTermino = $dataEncerrar->diffInDays($now);

                if ($dias == 0) {
                    $dias = "menos de 1 dia";
                    $aberturaAtiva["menosdeumdia"] = true;
                } else {
                    $aberturaAtiva["menosdeumdia"] = false;
                }

                if ($diasTermino == 0) {
                    $diasTermino = "menos de 1 dia";
                    $aberturaAtiva["menosdeumdiatermino"] = true;
                } else {
                    $aberturaAtiva["menosdeumdiatermino"] = false;
                }

                $aberturaAtiva["diasAteAbertura"] = $dias;
                $aberturaAtiva["diasAteTerminar"] = $diasTermino;

                $agora = $now->format('Y-m-d H:m:s');

                $dataInicio = ($aberturaAtiva->dataAbertura)->format('Y-m-d H:m:s');
                $dataFim = ($aberturaAtiva->dataEncerar)->format('Y-m-d H:m:s');

                if ($agora >= $dataInicio && $agora < $dataFim) {
                    $aberturaAtiva["isAberto"] = true;
                } else {
                    $aberturaAtiva["isAberto"] = false;
                }

                array_push($aberturasPorCurso[$aberturaAtiva->idCurso], $aberturaAtiva);
            }

            //turnos para mostrar na pagina de inscricao, tem de ir assim formatados...
            $inscri = Inscricao::where('inscricao.idUtilizador', ($request->user())->id)->join('turno','turno.id','=','inscricao.idTurno'
                                    )->join('cadeira','turno.idCadeira','=','cadeira.id')
                                    ->where('turno.idAnoletivo', '=', $anoletivo->id)->where('cadeira.semestre', $anoletivo->semestreativo)
                                    //adicionada estas 2 linhsa abaixo senao quando a pessoa ja estava aprovada (fim do semestre) mostrava a uc e n faz sentido msotrar
                                    ->join('inscricaoucs','cadeira.id', '=', 'inscricaoucs.idCadeira')
                                    ->where('inscricaoucs.idUtilizador',$request->user()->id)->where('inscricaoucs.estado', 1)
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

            $idTurnos = Inscricao::where('idUtilizador',Auth::user()->id)->
                                join('turno','turno.id','=','inscricao.idTurno')->where('turno.idAnoletivo', $anoletivo->id)
                                ->join('cadeira','turno.idCadeira','=','cadeira.id')->where('cadeira.semestre', $anoletivo->semestreativo)
                                ->pluck('inscricao.idTurno')->toArray();

            $horario = $this->getHorariosTodasAsAulas();
            $horariopessoal = (new InscricaoController)->getHorarioPessoal($idTurnos);

            return response(["cursos" => $cursos, "inscricoes" => $ins, "aberturas" => $aberturasPorCurso, "inscricoesTurnosAtuais" => $insToSend, 'horario' => $horario,'horariopessoal' => $horariopessoal],200);
        }
    }

    public function getHorariosTodasAsAulas(){
        $anoletivo = Anoletivo::where('ativo',1)->first();
        $idTurnos = Turno::join('cadeira','turno.idCadeira','=','cadeira.id')->where('cadeira.semestre', $anoletivo->semestreativo)
        ->join('inscricaoucs','cadeira.id', '=', 'inscricaoucs.idCadeira')
        ->where('inscricaoucs.idUtilizador',Auth::user()->id)->where('inscricaoucs.idAnoletivo', $anoletivo->id)
        ->pluck('turno.id')->toArray();

        $query = DB::raw("(CASE WHEN numero='0' THEN '' ELSE numero END) as content");
        $aulas = Aula::whereIn('idTurno',$idTurnos)->join('turno','turno.id','=','aula.idTurno')->join('cadeira','turno.idCadeira','=','cadeira.id')
        ->whereNotNull('horaInicio')->orderby('data')->orderby('horaInicio')->orderby('horaInicio')
        ->select(DB::raw("CONCAT(aula.data,' ',horaInicio) AS start"),DB::raw("CONCAT(data,' ',horaFim) AS end"),"cadeira.nome as title",DB::raw("CONCAT(tipo,(CASE WHEN numero='0' THEN '' ELSE numero END)) as content"))->get();
        
        $data = Aula::whereIn('idTurno',$idTurnos)->join('turno','turno.id','=','aula.idTurno')->join('cadeira','turno.idCadeira','=','cadeira.id')
        ->whereNotNull('horaInicio')->orderby('data')->orderby('horaInicio')->orderby('horaInicio')
        ->select('data')->first();
        
        return ["data" => empty($data) ? null : $data->data, "horario"=> $aulas];
    }

    public function getCadeirasUtilizadorConfirmar(Request $request){
        if($request->user()->tipo == 0){ //estudante
            InscricaoucsResource::$format = 'cadeiras';
            $anoletivo = Anoletivo::where('ativo',1)->first();
            $dados = Inscricaoucs::where('inscricaoucs.idUtilizador',($request->user())->id)->where('inscricaoucs.idAnoletivo',$anoletivo->id)
                            ->where('estado',1)->join('cadeira','inscricaoucs.idCadeira','=','cadeira.id')
                            ->where('cadeira.semestre', $anoletivo->semestreativo)->join('curso', 'curso.id','=','cadeira.idCurso')->get();
            /* $dados = aberturas::where('aberturas.dataAbertura', '<=', Carbon::now())->where('aberturas.dataEncerar', '>=', Carbon::now())->join('curso')
                            ->join('curso', 'curso.id','=','aberturas.idCurso') */
            $inscricaoucs = InscricaoucsResource::collection($dados);   

            $cursos = [];
            foreach ($inscricaoucs as $key => $inscricao) {
                if(!array_key_exists($inscricao->idCurso,$cursos)){
                    $cursos[$inscricao->idCurso] = [];
                }
                array_push($cursos[$inscricao->idCurso], $inscricao);
            }
            //dd($cursos);
            $user = Utilizador::where('id', ($request->user())->id)->first();
            $infoPedidos = [];

            $now = Carbon::now('Europe/Lisbon');
            $isOpen = Aberturas::where('dataAbertura', '<=', $now)->where('dataEncerar', '>=', $now)
            ->whereNull('deleted_at')->where('tipoAbertura', 0)->where('idCurso', Auth::user()->curso->id)->get();

            $pedidosAtivo = Aberturas::where('aberturas.dataEncerar', '>=', $now)
            ->whereNull('deleted_at')->where('tipoAbertura', 0)->where('idCurso', Auth::user()->curso->id)
            ->select('dataAbertura', 'dataEncerar')->get();

            if (sizeof($pedidosAtivo) != 0) {
                $pedidosAtivo = $pedidosAtivo[0];

                $dataAbertura = Carbon::parse($pedidosAtivo->dataAbertura);
                $dataEncerrar = Carbon::parse($pedidosAtivo->dataEncerar);

                $now = Carbon::now('Europe/Lisbon');
                $dias = $dataAbertura->diffInDays($now);
                $diasTermino = $dataEncerrar->diffInDays($now);

                if ($dias == 0) {
                    $dias = "menos de 1 dia";
                    $pedidosAtivo["menosdeumdia"] = true;
                } else {
                    $pedidosAtivo["menosdeumdia"] = false;
                }

                if ($diasTermino == 0) {
                    $diasTermino = "menos de 1 dia";
                    $pedidosAtivo["menosdeumdiatermino"] = true;
                } else {
                    $pedidosAtivo["menosdeumdiatermino"] = false;
                }

                $pedidosAtivo["diasAteAbertura"] = $dias;
                $pedidosAtivo["diasAteTerminar"] = $diasTermino;
            } else {
                $pedidosAtivo = [];
            }
            
            if (sizeof($isOpen) != 0) {
                $isOpen = true;
            } else {
                $isOpen = false;
            }

            return response(["cursos" => $cursos, "pedidos" => PedidosResource::collection($user->pedidos), "infoPedidos" => $pedidosAtivo, "periodo" => $isOpen],200);
        }
    }

    public function getCadeirasProfessor(Anoletivo $anoletivo, $semestre){
        /*if(!(new CoordenadorService)->isProfessor($cadeira)){
            return response("Não tem permissão para aceder a esta unidade curricular",401);
        }*/
        //$subquery = "(select count(*) from inscricao where idTurno = turno.id) as vagas";
        $idsCadeiras = Cadeira::join('turno','turno.idCadeira','=','cadeira.id')
                        ->join('aula','aula.idTurno','=','turno.id')->where('turno.idAnoletivo', $anoletivo->id)
                        ->where('aula.idProfessor',Auth::user()->id)->distinct('cadeira.id')->pluck('cadeira.id')->toArray();
        $turnos = Curso::join('cadeira', 'curso.id', '=', 'cadeira.idCurso')->join('turno','turno.idCadeira','=','cadeira.id')
                        ->where('turno.idAnoletivo', $anoletivo->id)->whereIn('cadeira.id', $idsCadeiras)
                        ->select('cadeira.*','turno.*','curso.nome as nomeCurso', 'curso.codigo as codigoCurso', 'turno.vagasocupadas as vagas')
                        ->orderBy('tipo', 'DESC')->orderBy('numero', 'ASC')->distinct('turno.id')->get();
        /*$turnos = Curso::join('cadeira', 'curso.id', '=', 'cadeira.idCurso')->join('turno','turno.idCadeira','=','cadeira.id')
            ->join('aula','aula.idTurno','=','turno.id')->where('turno.idAnoletivo', $anoletivo->id)->where('aula.idProfessor',Auth::user()->id)
            ->select('cadeira.*','turno.*','curso.nome as nomeCurso', 'curso.codigo as codigoCurso', DB::raw($subquery))->distinct('turno.id')->get();*/
        $dados = [];
        foreach ($turnos as $key => $turno) {
            if(!array_key_exists($turno->idCurso,$dados)){
                $dados[$turno->idCurso] = ["curso" => $turno->nomeCurso, "codigoCurso" => $turno->codigoCurso,  "cadeiras" => []];
            }
            if(!array_key_exists($turno->idCadeira,$dados[$turno->idCurso]["cadeiras"])){
                $dados[$turno->idCurso]["cadeiras"][$turno->idCadeira] = [];
            }
            array_push($dados[$turno->idCurso]["cadeiras"][$turno->idCadeira], $turno);
        }
        return response($dados,200);
    }

    public function getStatsCadeiraProfessor(Cadeira $cadeira, Anoletivo $anoletivo){
        if(!(new CoordenadorService)->isProfessorCadeira($cadeira)){
            return response("Não tem permissão para aceder a esta unidade curricular",401);
        }
        $result = (new CadeiraService)->getInformacoesCadeirasForAdmin($cadeira, $anoletivo);

        return response($result["msg"],$result["code"]);
    }

    public function getCadeiraProfessor(Cadeira $cadeira, Anoletivo $anoletivo){
        if(!(new CoordenadorService)->isProfessorCadeira($cadeira)){
            return response("Não tem permissão para aceder a esta unidade curricular",401);
        }
        
        CadeiraResource::$format = 'paraprofessor';
        $cadeira = CadeiraResource::make($cadeira)->anoletivo($anoletivo->id,$cadeira->semestre);
        return response(["cadeira" => $cadeira],200);
    }

    public function getCadeirasNaoAprovadasUtilizador(Utilizador $utilizador){
        if($utilizador->tipo == 0){ //estudante
            InscricaoucsResource::$format = 'cadeiras';
            $anoletivo = Anoletivo::where('ativo',1)->first();
            $dados = Cadeira::where('idCurso', $utilizador->idCurso)->leftJoin('inscricaoucs', function ($join) use(&$utilizador) {
                $join->on('cadeira.id', '=', 'inscricaoucs.idCadeira')
                     ->where('inscricaoucs.idUtilizador','=',$utilizador->id);//->where('inscricaoucs.estado','1');
            })->where('cadeira.semestre',$anoletivo->semestreativo)
            ->select('inscricaoucs.*', 'cadeira.*' )->get();
            $copy =  $dados;
            foreach ($copy as $key => $value) {
                if($value->estado == 2){
                    unset($dados[$key]);
                }
            }
            CadeiraResource::$format = 'inscricaoucsuser';
            return response(CadeiraResource::collection($dados),200);
        }
    }

    public function getInformacoesCadeira(Cadeira $cadeira, Anoletivo $anoletivo){
        $result = (new CadeiraService)->getInformacoesCadeirasForAdmin($cadeira, $anoletivo);

        return response($result["msg"],$result["code"]);
    }

    public function getCadeira(Cadeira $cadeira, Anoletivo $anoletivo){
        $tiposturnos = Turno::where('idCadeira', $cadeira->id)->where('idAnoletivo',$anoletivo->id)->select("tipo", DB::raw('count(*) as total'))->groupby("tipo")->pluck('tipo','total')->toArray();
        $totalinscritos = Inscricaoucs::where('idCadeira', $cadeira->id)->where('estado', 1)->where('idAnoletivo', $anoletivo->id)->select(DB::raw('count(*) as total'))->get();
        $numAlunos = $totalinscritos[0]->total;
        $data=[];
        foreach ($tiposturnos as $key => $value){
            array_push($data,["turno" => $value,"numeroturnos" => $key, "mediavagas" => round($numAlunos/$key)]);
        }

        $turnos = Cadeira::where('cadeira.id',$cadeira->id)->join('turno','turno.idCadeira','=','cadeira.id')
                        ->where('turno.idAnoletivo', $anoletivo->id)->where('turno.visivel', 1)->count();

        $isVisivel = false;

        if ($turnos > 0) {
            $isVisivel = true;
        } else {
            $isVisivel = false;
        }
        
        $cadeiras = CadeiraResource::make($cadeira)->anoletivo($anoletivo->id,$cadeira->semestre);
        return response(["info" => $data, "cadeiras" => $cadeiras, "isVisivel" => $isVisivel],200);
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

    public function tornarInvisivel(Cadeira $cadeira,Anoletivo $anoletivo, $visivel){
        if($visivel != 0 && $visivel != 1){
            return response("Os dados enviados não são validos!",401);
        }

        $result = (new CadeiraService)->mudarVisibilidade($cadeira,$anoletivo,$visivel);

        return response($result["msg"],$result["code"]);
    }
}
