<?php

namespace App\Services;

use DateTime;
use App\Models\Logs;
use App\Models\Aberturas;

class AberturaService
{
    // verifica todas as aberturas do curso e apaga todas as nao necessarias
    public function checkForOldAberturas($curso){
        $now = new DateTime();
        foreach($curso->aberturas as $abertura){
            if($abertura->dataEncerar < $now) {
                $abertura->delete();
            }
        }
    }

    public function checkIfAberturaCanBeCreated($curso,$data){
        //se for exatamenteigual da return de um erro
        foreach($curso->aberturas as $abertura){
            if($abertura->ano == $data->get('ano')){
                if($abertura->tipoAbertura == $data->get('tipoAbertura')){
                    if($abertura->dataEncerar > $data->get('dataAbertura')){
                        return ["codigo"=>0,"error"=>"Já existe um periodo aberto."];
                    }
                }
            }
        }
        if($data->get('ano') == 0){
            foreach($curso->aberturas as $abertura){
                if($abertura->tipoAbertura == $data->get('tipoAbertura')){
                    return ["codigo"=>0,"error"=>"Já existe um periodo aberto para algum ano, não é possivel abrir para todos os anos."];
                }
            }
        }else{
            foreach($curso->aberturas as $abertura){
                if($abertura->ano == 0){
                    return ["codigo"=>0,"error"=>"Já existe um periodo aberto para todos os anos."];
                }
            }
        }
        $now = new DateTime();
        if($data->get('dataAbertura') < $now){
            return ["codigo"=>0,"error"=>"A data de abertura é anteriora data atual."];
        }
        
        return ["codigo"=>1];
    }

    public function save($curso, $data){
        //'dataAbertura', 'dataEncerar', 'ano', 'tipoAbertura', 'idUtilizador', 'idCurso'
        $abertura = new Aberturas();
        $abertura->dataAbertura = $data->get('dataAbertura');
        $abertura->dataEncerar = $data->get('dataEncerar');
        $abertura->ano = $data->get('ano');
        $abertura->tipoAbertura = $data->get('tipoAbertura');
        $abertura->idUtilizador = $data->get('idUtilizador');
        $abertura->idCurso = $curso->id;
        $abertura->save();
        return $abertura;
    }

    public function remove($abertura){
        return $abertura->delete();
    }
}