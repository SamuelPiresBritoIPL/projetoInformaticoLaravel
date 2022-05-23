<?php

namespace App\Services;

use App\Models\Logs;
use App\Models\Cadeira;
use App\Models\Pedidos;
use App\Models\Anoletivo;
use App\Models\Pedidosucs;
use App\Models\Utilizador;
use App\Models\Inscricaoucs;

class CursoService
{
    public function getAnosCurso($idCurso){
        return Cadeira::where('idCurso',$idCurso)->select('ano')->distinct()->get()->max('ano');
    }
    public function getPedidosCurso($idCurso){
        return Pedidos::join('curso', 'idCurso' ,'=', 'curso.id')
        ->where('curso.id', $idCurso)->where('estado', "=", "1")->count();
    }
}