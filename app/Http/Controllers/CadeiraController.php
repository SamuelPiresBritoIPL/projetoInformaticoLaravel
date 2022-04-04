<?php

namespace App\Http\Controllers;

use App\Http\Resources\InscricaoucsResource;
use App\Models\Inscricaoucs;
use App\Models\Utilizador;

class CadeiraController extends Controller
{
    public function getCadeirasUtilizador(Utilizador $utilizador){
        if($utilizador->tipo == 0){ //estudante
            InscricaoucsResource::$format = 'cadeiras';
            return response(InscricaoucsResource::collection($utilizador->inscricaoucs),200);
        }
    }

    public function getCadeirasNaoAprovadasUtilizador(Utilizador $utilizador){
        if($utilizador->tipo == 0){ //estudante
            InscricaoucsResource::$format = 'cadeiras';
            
            return response(InscricaoucsResource::collection($utilizador->inscricaoucs),200);
        }
    }
}
