<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Services\TurnoService;

class TurnoController extends Controller
{
    public function getInformacoesTurnos(Turno $turno){
        $result = (new TurnoService)->getInformacoesTurnoForAdmin($turno);

        return response($result["msg"],$result["code"]);
    }
}
