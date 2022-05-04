<?php

namespace App\Http\Controllers;

use App\Models\Anoletivo;
use App\Models\Utilizador;
use App\Services\EstudanteService;

class EstudanteController extends Controller
{
    public function getDados(Utilizador $estudante, Anoletivo $anoletivo, $semestre){
        if($semestre != 1 && $semestre != 2){
            return response("O semestre não é válido");
        }

        $result = (new EstudanteService)->getDadosEstudante($estudante,$anoletivo,$semestre);

        return response($result["msg"],$result["code"]);
    }

}
