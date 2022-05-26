<?php

namespace App\Http\Controllers;

use App\Models\Anoletivo;
use App\Models\Utilizador;
use App\Services\EstudanteService;

class EstudanteController extends Controller
{
    public function getDados($estudante, Anoletivo $anoletivo, $semestre){
        $estudante = Utilizador::where('login', $estudante)->first();
        if(empty($estudante)){
            return response("O login não foi encontrado",400);
        } 
        if (!$estudante->isEstudante()) {
            return response("Este utilizador não é um aluno.",400);
        }
        if($semestre != 1 && $semestre != 2){
            return response("O semestre não é válido",400);
        }

        $result = (new EstudanteService)->getDadosEstudante($estudante,$anoletivo,$semestre);

        return response($result["msg"],$result["code"]);
    }

}
