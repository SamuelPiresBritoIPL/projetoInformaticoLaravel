<?php

namespace App\Http\Controllers;

use App\Models\curso;
use App\Models\turno;
use App\Models\cadeira;
use App\Models\utilizador;
use App\Models\inscricaoucs;
use App\Services\WebserviceService;
use Illuminate\Http\Request;

class WebserviceController extends Controller
{
    public function getCursos()
    {
        $url = (new WebserviceService)->makeUrl(config('services.webapiurls.cursos'),config('services.webapiurls.cursokeys'));

    	$json = (new WebserviceService)->callAPI("Get",$url);
        
        if(empty($json)){
            return response("something went wrong", 401);
        }

        $newDataAdded = (new WebserviceService)->getCursos($json);
        
        return response($newDataAdded, 200);
    }

    public function getInscricoesturnos(){
        set_time_limit(500);

        $url = (new WebserviceService)->makeUrl(config('services.webapiurls.turnos'),config('services.webapiurls.turnokeys'));

    	$json = (new WebserviceService)->callAPI("Get",$url);

        if(empty($json)){
            return response("something went wrong", 401);
        }

        $data = (new WebserviceService)->getInscricoesturnos($json);
        
        return response(["cursonotfound" => $data['cursonotfound'], "cadeiranotfound" => $data['cadeiranotfound'], "newStudentAdded" => $data['newStudentAdded'], "novasinscricoes" => $data['newDataAdded']], 200);
    }
}
