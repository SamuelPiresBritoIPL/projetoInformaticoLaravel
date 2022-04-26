<?php

namespace App\Services;

use App\Models\Logs;
use App\Models\Cadeira;
use App\Models\Anoletivo;
use App\Models\Utilizador;
use App\Models\Inscricaoucs;

class CursoService
{
    public function getAnosCurso($idCurso){
        return Cadeira::where('idCurso',$idCurso)->select('ano')->distinct()->get()->max('ano');
    }
}