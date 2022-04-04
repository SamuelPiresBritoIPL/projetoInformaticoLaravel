<?php

namespace App\Services;

use App\Models\Cadeira;
use App\Models\Logs;

class CursoService
{
    public function getAnosCurso($idCurso){
        return Cadeira::where('idCurso',$idCurso)->select('ano')->distinct()->get()->max('ano');
    }
}