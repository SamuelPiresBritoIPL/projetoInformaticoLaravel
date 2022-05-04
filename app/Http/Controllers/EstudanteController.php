<?php

namespace App\Http\Controllers;

use App\Models\Anoletivo;
use App\Models\Utilizador;

class EstudanteController extends Controller
{
    public function getDados(Utilizador $estudante, Anoletivo $anoletivo, $semestre){
        if($semestre != 1 && $semestre != 2){
            return response("O semestre não é válido");
        }

        return response(200);
    }

}
