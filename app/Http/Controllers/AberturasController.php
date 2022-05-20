<?php

namespace App\Http\Controllers;

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
}
