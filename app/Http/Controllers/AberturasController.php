<?php

namespace App\Http\Controllers;

use App\Http\Requests\AberturaPostRequest;
use App\Models\curso;
use Illuminate\Http\Request;
use App\Http\Resources\CursoResource;
use App\Models\Aberturas;

class AberturasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addAberturas(AberturaPostRequest $request, Curso $curso){
        $data = collect($request->validated());

        //para nao se criar uma abertura igual
        foreach($curso->aberturas as $abertura){
            if($abertura->ano == $data->get('ano')){
                if($abertura->tipoAbertura == $data->get('tipoAbertura')){
                    if($abertura->dataEncerar > $data->get('dataAbertura')){
                        return response("Já existe um periodo aberto",401);
                    }
                }
            }
        }

        

        if(count($curso->aberturas) == 0){
            if($data->get('tipoAbertura') == 1){
                return response("Tem de ser aberta o periodo de confirmação de ucs antes de abrir o periodo de inscrição aos turnos.",401);
            }
        }else{
            
        }
    }
}
