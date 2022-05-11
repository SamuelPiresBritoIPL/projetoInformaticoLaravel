<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Services\TurnoService;
use App\Http\Requests\TurnoPostRequest;

class TurnoController extends Controller
{
    public function getInformacoesTurnos(Turno $turno){
        $result = (new TurnoService)->getInformacoesTurnoForAdmin($turno);

        return response($result["msg"],$result["code"]);
    }

    public function getInformacoesTurnosProfessores(Turno $turno){
        $result = (new TurnoService)->getInformacoesTurnoForAdmin($turno);

        return response($result["msg"],$result["code"]);
    }

    public function editTurno(TurnoPostRequest $request, Turno $turno){
        $data = collect($request->validated());
        
        $result = (new TurnoService)->editTurno($data,$turno);

        return response($result["msg"],$result["code"]);
    }

    public function exportTurno(Turno $turno){
        $result = (new TurnoService)->exportTurno($turno);

        return response($result["msg"],$result["code"]);
    }
}
