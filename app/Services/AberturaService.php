<?php

namespace App\Services;

use DateTime;
use Carbon\Carbon;
use App\Models\Logs;
use App\Models\Curso;
use App\Models\Turno;
use App\Models\Cadeira;
use App\Models\Aberturas;
use Illuminate\Support\Facades\Auth;

class AberturaService
{
    // verifica todas as aberturas do curso e apaga todas as nao necessarias
    public function checkForOldAberturas($curso){
        $now = new DateTime();
        foreach($curso->aberturas as $abertura){
            if($abertura->dataEncerar->isPast()) {
                $abertura->delete();
            }
        }
    }

    public function checkIfAberturaCanBeCreated($curso,$data){
        //se for exatamenteigual da return de um erro
        $abertura = Aberturas::where('idCurso', $curso->id)->where('ano',$data->get('ano'))->where('tipoAbertura',$data->get('tipoAbertura'))->where('semestre',$data->get('semestre'))->where('idAnoletivo',$data->get('idAnoletivo'))->first();
        if(!empty($abertura)){
            return ["codigo"=>0,"error"=>"Já existe um periodo aberto."];
        }
        
        if($data->get('ano') == 0){
            foreach($curso->aberturas as $abertura){
                if($abertura->tipoAbertura == $data->get('tipoAbertura')){
                    return ["codigo"=>0,"error"=>"Já existe um periodo aberto para algum ano, não é possivel abrir para todos os anos."];
                }
            }
        }else{
            foreach($curso->aberturas as $abertura){
                if($abertura->ano == 0 && $abertura->tipoAbertura == $data->get('tipoAbertura')){
                    return ["codigo"=>0,"error"=>"Já existe um periodo aberto para todos os anos."];
                }
            }
        }

        if(Carbon::parse($data->get('dataAbertura')) <= Carbon::now('Europe/Lisbon')){
            return ["codigo"=>0,"error"=>"A data de abertura é anterior a data atual."];
        }

        if($data->get('tipoAbertura') == 1){
            if($data->get('ano') == 0){
                $cadeiras = Cadeira::where('idCurso', $curso->id)->where('semestre',$data->get('semestre'))->pluck('id')->toArray();
            }else{
                $cadeiras = Cadeira::where('idCurso', $curso->id)->where('semestre',$data->get('semestre'))->where('ano',$data->get('ano'))->pluck('id')->toArray();
            }
            
            $turnos = Turno::wherein('idCadeira',$cadeiras)->join('cadeira', 'cadeira.id', '=', 'turno.idCadeira')->where('turno.idAnoletivo',$data->get('idAnoletivo'))->where('visivel',1)->whereNull('vagastotal')->get();
            if($turnos->isNotEmpty()){
                $msg = "Turnos que faltam definir vagas: ";
                $cursos = [];
                foreach ($turnos as $key => $turno) {
                    if(!array_key_exists($turno->idCadeira,$cursos)){
                        $cursos[$turno->idCadeira] = [];
                    }
                    array_push($cursos[$turno->idCadeira], $turno);
                }

                foreach ($cursos as $key => $turnos2) {
                    $msg .= "\n" . $turnos2[0]->nome . ": ";
                    foreach ($turnos2 as $key => $turno) {
                        $msg .= $turno->tipo . $turno->numero . ", ";
                    }
                }
                return ["codigo"=>0,"error"=> $msg];
            }
        }
        
        return ["codigo"=>1];
    }

    public function checkIfAberturaCanBeUpdated($abertura,$data){
        if($data->has("dataAbertura")) {
			if(Carbon::parse($data->get('dataAbertura')) <= Carbon::now('Europe/Lisbon')){
                $errors = [];
                $errors["dataAbertura"] = ["A data de abertura não pode ser alterada para uma data ja ocorrida."];
                return ["codigo"=>0,"error"=>$errors];
            }
		}

        if($data->has("ano")){
            if($abertura->ano != $data->get('ano')){
                $curso = Curso::where('id', $abertura->idCurso)->first();
                if($data->get('ano') == 0){
                    foreach($curso->aberturas as $aberturaCurso){
                        if($abertura->id != $aberturaCurso->id && $abertura->tipoAbertura == $aberturaCurso->tipoAbertura){
                            return ["codigo"=>0,"error"=>"Já existe um periodo aberto para algum ano, não é possivel abrir para todos os anos."];
                        }
                    }
                }else{
                    foreach($curso->aberturas as $aberturaCurso){
                        if($abertura->id != $aberturaCurso->id && $abertura->tipoAbertura == $aberturaCurso->tipoAbertura && $abertura->ano == 0){
                            return ["codigo"=>0,"error"=>"Já existe um periodo aberto para todos os anos."];
                        }
                    }
                }
            }
        }
        
        return ["codigo"=>1];
    }

    public function save($curso, $data){
        $abertura = new Aberturas();
        $abertura->dataAbertura = $data->get('dataAbertura');
        $abertura->dataEncerar = $data->get('dataEncerar');
        $abertura->ano = $data->get('ano');
        $abertura->semestre = $data->get('semestre');
        $abertura->tipoAbertura = $data->get('tipoAbertura');
        $abertura->idUtilizador = Auth::user()->id;
        $abertura->idAnoletivo = $data->get('idAnoletivo');
        $abertura->idCurso = $curso->id;
        $abertura->save();
        return $abertura;
    }

    public function update($abertura, $data){
        if($data->has("dataAbertura")) {
            $abertura->dataAbertura = $data->get('dataAbertura');
            $abertura->dataEncerar = $data->get('dataEncerar');
        }
        if($data->has("ano")) {
            $abertura->ano = $data->get('ano');
        }
        $abertura->save();
        return $abertura;
    }

    public function remove($abertura){
        return $abertura->delete();
    }
}