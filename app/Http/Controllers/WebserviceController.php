<?php

namespace App\Http\Controllers;

use App\Models\cadeira;
use Exception;
use App\Models\curso;
use App\Models\utilizador;
use Illuminate\Http\Request;

class WebserviceController extends Controller
{
    public function getCursos()
    {
    	$json = $this->callAPI("Get","http://www.dei.estg.ipleiria.pt/intranet/horarios/ws/inscricoes/turnos.php?anoletivo=202122&periodo=S2");
        if(empty($json)){
            return;
            //dd("teste");
        }
        //dd($json[0]);
        foreach ($json as $turno) {
            $curso = curso::where('codigo',$turno->CD_Curso)->first();
            if(empty($curso)){
                $curso = new curso();
                $curso->codigo = $turno->CD_Curso;
                $curso->nome = $turno->NM_CURSO;
                $curso->save();
            }

            $utilizador = utilizador::where('login', $turno->LOGIN)->first();
            if(empty($utilizador)){
                $utilizador = new utilizador();
                $utilizador->nome = $turno->NM_FUNCIONARIO;
                $utilizador->login = $turno->LOGIN;
                $utilizador->idCurso = $curso->id;
                $utilizador->tipo = 1;
                $utilizador->save();
            }
            //->where('tipo',$turno->CodDiscipTipo)
            $cadeira = cadeira::where('codigo',$turno->CD_Discip)->first();
            if(empty($cadeira)){
                $cadeira = new cadeira();
                $cadeira->codigo = $turno->CD_Discip;
                $cadeira->ano = $turno->AnoCurricular;
                $cadeira->semestre = str_split($turno->Periodo)[1];
                dd($cadeira);
                if($turno->CodDiscipTipo == "TP" || $turno->CodDiscipTipo == "PL"){
                    $cadeira->nome = substr($turno->DS_Discip, 0, -5);
                }else{
                    $cadeira->nome = substr($turno->DS_Discip, 0, -4);
                }
                $cadeira->anoletivo = $turno->CD_Lectivo;
                $cadeira->idCurso = $curso->id;
                $cadeira->save();
            }
        }
    }

    public function callAPI($method, $url){
        try {
            $response = file_get_contents($url);
            if(empty($response)){
                return;
            }
            $response = json_decode($response);
            return $response;
        }
        catch (Exception $e) {
            return;
        }
    }
}
