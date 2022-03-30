<?php

namespace App\Services;

use App\Models\Logs;

class LogsService
{
    public function save($descricao,$tabela,$idUtilizador){
        $log = new Logs();
        $log->descricao = $descricao;
        $log->tabela = $tabela;
        $log->idUtilizador = $idUtilizador;
        $log->save();
    }
}