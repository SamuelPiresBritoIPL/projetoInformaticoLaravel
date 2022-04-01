<?php

namespace App\Services;

use App\Models\Anoletivo;
use App\Models\Aula;
use Exception;
use App\Models\Logs;
use App\Models\Curso;
use App\Models\Turno;
use App\Models\Cadeira;
use App\Models\Utilizador;
use App\Models\Inscricaoucs;

class WebserviceService
{
    public function makeUrl($url,$keys){
        foreach($keys as $key => $value){
            $url = $url . $key . "=" . $value . "&";
        }
        return $url;
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

    public function getCursos($json){
        $newDataAdded = 0;
        $updatedData = 0;
        Aula::truncate();
        foreach ($json as $turno) {
            $curso = Curso::where('codigo',$turno->CD_Curso)->first();
            if(empty($curso)){
                $curso = new Curso();
                $curso->codigo = $turno->CD_Curso;
                $curso->nome = $turno->NM_CURSO;
                $curso->save();
                $newDataAdded += 1;
            }else{
                $curso->nome = $turno->NM_CURSO;
                $curso->save();
                $updatedData += 1;
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
                $cadeira->semestre = str_split($turno->Periodo)[1];
                if($turno->CodDiscipTipo == "TP" || $turno->CodDiscipTipo == "PL"){
                    $cadeira->nome = substr($turno->DS_Discip, 0, -5);
                }else{
                    $cadeira->nome = substr($turno->DS_Discip, 0, -4);
                }
                $cadeira->idCurso = $curso->id;
                $cadeira->save();
                $newDataAdded += 1;
            }else{
                $cadeira->ano = $turno->AnoCurricular;
                $cadeira->semestre = str_split($turno->Periodo)[1];
                if($turno->CodDiscipTipo == "TP" || $turno->CodDiscipTipo == "PL"){
                    $cadeira->nome = substr($turno->DS_Discip, 0, -5);
                }else{
                    $cadeira->nome = substr($turno->DS_Discip, 0, -4);
                }
                $cadeira->idCurso = $curso->id;
                $cadeira->save();
                $newDataAdded += 1;
            }
            
            $anoletivo = Anoletivo::where('anoletivo',$turno->CD_Lectivo)->first();
            if(empty($anoletivo)){
                $anoletivo = new Anoletivo();
                $anoletivo->anoletivo = $turno->CD_Lectivo;
                $anoletivo->save();
            }

            $newturno = Turno::where('idCadeira',$cadeira->id)->where('tipo',$turno->CodDiscipTipo)->where('numero',$turno->CDTurno)->where('idAnoletivo',$anoletivo->id)->first();
            if(empty($newturno)){
                $newturno = new Turno();
                $newturno->idCadeira = $cadeira->id;
                $newturno->idAnoletivo = $anoletivo->id;
                $newturno->tipo = $turno->CodDiscipTipo;
                $newturno->numero = $turno->CDTurno;
                $newturno->save();
                $newDataAdded += 1;
            }

            $aula = new Aula();
            $aula->idTurno = $newturno->id;
            $aula->idProfessor = $utilizador->id;
            $aula->save();
            $newDataAdded += 1;
        }
        return $newDataAdded;
    }

    public function getInscricoesturnos($json){
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

            $anoletivo = Anoletivo::where('anoletivo',$inscricao->CD_LECTIVO)->first();
            if(empty($anoletivo)){
                $anoletivo = new Anoletivo();
                $anoletivo->anoletivo = $inscricao->CD_Lectivo;
                $anoletivo->save();
            }
            
            $inscricaoucs = Inscricaoucs::where('idCadeira', $cadeira->id)->where('idUtilizador', $utilizador->id)->where('idAnoletivo',$anoletivo->id)->first();
            if(empty($inscricaoucs)){
                $newDataAdded += 1;
                $inscricaoucs = new Inscricaoucs();
                $inscricaoucs->idCadeira = $cadeira->id;
                $inscricaoucs->idUtilizador = $utilizador->id;
                $inscricaoucs->idAnoletivo = $anoletivo->id;
                $inscricaoucs->nrinscricoes = $inscricao->NR_INSCRICOES;
            }
            $inscricaoucs->nrinscricoes = $inscricao->NR_INSCRICOES;
            $inscricaoucs->save();
        }
        return[
            'newStudentAdded' => $newStudentAdded,
            'cursonotfound' => $cursonotfound,
            'cadeiranotfound' => $cadeiranotfound,
            'newDataAdded' => $newDataAdded,
        ];
    }
}