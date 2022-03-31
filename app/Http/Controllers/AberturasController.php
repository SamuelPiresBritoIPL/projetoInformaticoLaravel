<?php

namespace App\Http\Controllers;

use App\Http\Requests\AberturaPostRequest;
use App\Models\curso;
use Illuminate\Http\Request;
use App\Http\Resources\CursoResource;
use App\Models\Aberturas;
use App\Services\AberturaService;

class AberturasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addAberturas(AberturaPostRequest $request, Curso $curso){
        $data = collect($request->validated());
        //apagar aberturas antigas
        (new AberturaService)->checkForOldAberturas($curso);

        $canCreate = (new AberturaService)->checkIfAberturaCanBeCreated($curso, $data);
        if($canCreate["codigo"] == 0){
            response($canCreate["error"],401);
        }

        
        //fazer a validacao se se abre primeiro o periodo de confirmacao e apenas depois se abre a inscricao de turnos
        if(count($curso->aberturas) == 0){
            if($data->get('tipoAbertura') == 1){
                return response("Tem de ser aberta o periodo de confirmação de ucs antes de abrir o periodo de inscrição aos turnos.",401);
            }
        }else{
            
        }

        $abertura = (new AberturaService)->save($curso,$data);
        return response($abertura, 200);
    }
}
