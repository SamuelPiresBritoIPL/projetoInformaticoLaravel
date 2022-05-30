<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Curso;
use App\Models\Aberturas;
use Illuminate\Http\Request;
use App\Services\LogsService;
use App\Services\AberturaService;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CursoResource;
use App\Http\Requests\AberturaPostRequest;

class AberturasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(AberturaPostRequest $request, Curso $curso){
        $data = collect($request->validated());
        //apagar aberturas antigas
        (new AberturaService)->checkForOldAberturas($curso);

        $canCreate = (new AberturaService)->checkIfAberturaCanBeCreated($curso, $data);
        if($canCreate["codigo"] == 0){
            return response(["erros" => $canCreate["error"]],401);
        }

        //fazer a validacao se se abre primeiro o periodo de confirmacao e apenas depois se abre a inscricao de turnos
        $abertura = (new AberturaService)->save($curso,$data);
        (new LogsService)->save("Abertura criada do tipo ".$data->get('tipoAbertura')." do curso ".$abertura->curso->nome,"Aberturas",Auth::user()->id);
        return response($abertura, 201);
    }

    public function remove(Aberturas $abertura){
        if((new AberturaService)->remove($abertura))
            return response(200);
        return response(401);
    }

    public function update(AberturaPostRequest $request, Aberturas $abertura){
        $data = collect($request->validated());
        $canUpdate = (new AberturaService)->checkIfAberturaCanBeUpdated($abertura, $data);
        if($canUpdate["codigo"] == 0){
            return response($canUpdate["error"],401);
        }

        $abertura = (new AberturaService)->update($abertura,$data);
        //log
        return response($abertura, 200);
    }

    
    public function getInfoPeriodos(Request $request){
        $now = Carbon::now();
        $aberturasAbertas = Aberturas::whereDate('dataAbertura', '<=', $now)->whereDate('dataEncerar', '>=', $now)
        ->whereNull('deleted_at')->where('idCurso', Auth::user()->curso->id)->get();

        $aberturasAtivas = Aberturas::whereDate('aberturas.dataEncerar', '>=', $now)
        ->whereNull('deleted_at')->where('idCurso', Auth::user()->curso->id)
        ->get();

        $pedidosAtivo = [];
        $inscricoesAtivos = [];

        foreach ($aberturasAtivas as $key => $aberturaAtiva) {
            if ($aberturaAtiva->tipoAbertura == 0) {
                $pedidosAtivo = $aberturaAtiva;
    
                $dataAbertura = Carbon::parse($pedidosAtivo->dataAbertura);
                $dataEncerrar = Carbon::parse($pedidosAtivo->dataEncerar);
    
                $now = Carbon::now();
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
            } else
            if ($aberturaAtiva->tipoAbertura == 1) {
                $inscricoesAtivo = $aberturaAtiva;
    
                $dataAbertura = Carbon::parse($inscricoesAtivo->dataAbertura);
                $dataEncerrar = Carbon::parse($inscricoesAtivo->dataEncerar);
    
                $now = Carbon::now();
                $dias = $dataAbertura->diffInDays($now);
                $diasTermino = $dataEncerrar->diffInDays($now);
    
                if ($dias == 0) {
                    $dias = "menos de 1 dia";
                    $inscricoesAtivo["menosdeumdia"] = true;
                } else {
                    $inscricoesAtivo["menosdeumdia"] = false;
                }
    
                if ($diasTermino == 0) {
                    $diasTermino = "menos de 1 dia";
                    $inscricoesAtivo["menosdeumdiatermino"] = true;
                } else {
                    $inscricoesAtivo["menosdeumdiatermino"] = false;
                }
    
                $inscricoesAtivo["diasAteAbertura"] = $dias;
                $inscricoesAtivo["diasAteTerminar"] = $diasTermino;

                array_push($inscricoesAtivos, $inscricoesAtivo);
            }
        }

        $isPedidosOpen = false;
        $isInscricoesOpen = false;

        foreach ($aberturasAbertas as $key => $abertura) {
            if ($abertura->tipoAbertura == 0) {
                $isPedidosOpen = true;
            }
            if ($abertura->tipoAbertura == 1) {
                $isInscricoesOpen = true;
            } 
        }        
        
        return response(["infoPedidos" => $pedidosAtivo, "infoInscricoes" => $inscricoesAtivos, "isPedidosOpen" => $isPedidosOpen, "isInscricoesOpen" => $isInscricoesOpen], 200);
    }
}
