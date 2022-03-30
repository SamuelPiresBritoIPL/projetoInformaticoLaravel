<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\curso;
use App\Models\turno;
use App\Models\cadeira;
use App\Models\utilizador;
use App\Models\inscricaoucs;
use Illuminate\Http\Request;

class WebserviceController extends Controller
{
    public function getCursos()
    {
    	$json = $this->callAPI("Get","http://www.dei.estg.ipleiria.pt/intranet/horarios/ws/inscricoes/turnos.php?anoletivo=202122&periodo=S2");
        if(empty($json)){
            return response("something went wrong", 401);
        }
        $newDataAdded = 0;
        foreach ($json as $turno) {
            $curso = Curso::where('codigo',$turno->CD_Curso)->first();
            if(empty($curso)){
                $curso = new Curso();
                $curso->codigo = $turno->CD_Curso;
                $curso->nome = $turno->NM_CURSO;
                $curso->save();
                $newDataAdded += 1;
            }

            $utilizador = Utilizador::where('login', $turno->LOGIN)->first();
            if(empty($utilizador)){
                $utilizador = new Utilizador();
                $utilizador->nome = $turno->NM_FUNCIONARIO;
                $utilizador->login = $turno->LOGIN;
                $utilizador->idCurso = $curso->id;
                $utilizador->tipo = 1;
                $utilizador->save();
                $newDataAdded += 1;
            }
            
            $cadeira = Cadeira::where('codigo',$turno->CD_Discip)->first();
            if(empty($cadeira)){
                $cadeira = new Cadeira();
                $cadeira->codigo = $turno->CD_Discip;
                $cadeira->ano = $turno->AnoCurricular;
                $cadeira->semestre = str_split($turno->Periodo)[1];
                if($turno->CodDiscipTipo == "TP" || $turno->CodDiscipTipo == "PL"){
                    $cadeira->nome = substr($turno->DS_Discip, 0, -5);
                }else{
                    $cadeira->nome = substr($turno->DS_Discip, 0, -4);
                }
                $cadeira->anoletivo = $turno->CD_Lectivo;
                $cadeira->idCurso = $curso->id;
                $cadeira->save();
                $newDataAdded += 1;
            }

            $newturno = Turno::where('idCadeira',$cadeira->id)->where('idProfessor',$utilizador->id)->where('tipo',$turno->CodDiscipTipo)->where('numero',$turno->CDTurno)->first();
            if(empty($newturno)){
                $newturno = new Turno();
                $newturno->idCadeira = $cadeira->id;
                $newturno->idProfessor = $utilizador->id;
                $newturno->tipo = $turno->CodDiscipTipo;
                $newturno->numero = $turno->CDTurno;
                $newturno->save();
                $newDataAdded += 1;
            }
        }
        return response($newDataAdded, 200);
    }

    public function getInscricoesturnos(){
        set_time_limit(500);
        $json = $this->callAPI("Get","http://www.dei.estg.ipleiria.pt/intranet/horarios/ws/inscricoes/inscricoes_cursos.php?anoletivo=202122&estado=1");
        if(empty($json)){
            return response("something went wrong", 401);
        }
        $newStudentAdded = 0;
        $cursonotfound = 0;
        $cadeiranotfound = 0;
        $newDataAdded = 0;
        foreach ($json as $inscricao) {
            $curso = Curso::where('codigo',$inscricao->CD_CURSO)->first();
            if(empty($curso)){
                $cursonotfound += 1;
                continue;
            }

            $cadeira = Cadeira::where('codigo',$inscricao->CD_DISCIP)->first();
            if(empty($cadeira)){
                $cadeiranotfound += 1;
                continue;
            }

            $utilizador = Utilizador::where('login', $inscricao->LOGIN)->first();
            if(empty($utilizador)){
                $utilizador = new Utilizador();
                $utilizador->nome = $inscricao->NM_ALUNO;
                $utilizador->login = $inscricao->LOGIN;
                $utilizador->idCurso = $curso->id;
                $utilizador->tipo = 0;
                $utilizador->save();
                $newStudentAdded += 1;
            }
            
            $inscricaoucs = Inscricaoucs::where('idCadeira', $cadeira->id)->where('idUtilizador', $utilizador->id)->first();
            if(empty($inscricaoucs)){
                $newDataAdded += 1;
                $inscricaoucs = new Inscricaoucs();
                $inscricaoucs->idCadeira = $cadeira->id;
                $inscricaoucs->idUtilizador = $utilizador->id;
                $inscricaoucs->nrinscricoes = $inscricao->NR_INSCRICOES;
            }
            $inscricaoucs->nrinscricoes = $inscricao->NR_INSCRICOES;
            $inscricaoucs->save();
        }
        return response(["cursonotfound" => $cursonotfound, "cadeiranotfound" => $cadeiranotfound, "newStudentAdded" => $newStudentAdded, "novasinscricoes" => $newDataAdded], 200);
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
