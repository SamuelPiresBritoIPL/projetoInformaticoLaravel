<?php

namespace App\Http\Controllers;

use App\Http\Requests\WebservicePostRequest;
use App\Models\curso;
use App\Models\turno;
use App\Models\cadeira;
use App\Models\utilizador;
use App\Models\inscricaoucs;
use App\Services\WebserviceService;
use Illuminate\Http\Request;

class WebserviceController extends Controller
{
    public function getCursos(WebservicePostRequest $request)
    {
        $data = collect($request->validated());
        if(empty($data->get('semestre'))){
            return response("O semestre deve ser indicado para esta pedido", 401);
        }
        $url = (new WebserviceService)->makeUrl(config('services.webapiurls.cursos'),['anoletivo' => $data->get('anoletivo'),'periodo' => 'S'.$data->get('semestre')]);

    	$json = (new WebserviceService)->callAPI("Get",$url);
        
        if(empty($json)){
            return response("something went wrong", 401);
        }

        $newDataAdded = (new WebserviceService)->getCursos($json);
        
        return response($newDataAdded, 200);
    }

    public function getInscricoesturnos2(WebservicePostRequest $request){
        return $this->getInscricoesturnos($request, 2);
    }

    public function getInscricoesturnos(WebservicePostRequest $request, $estado=1){
        $data = collect($request->validated());

        set_time_limit(750);
        $url = (new WebserviceService)->makeUrl(config('services.webapiurls.turnos'),['anoletivo' => $data->get('anoletivo'),'estado' => $estado]);

    	$json = (new WebserviceService)->callAPI("Get",$url);

        if(empty($json)){
            return response("something went wrong", 401);
        }

        $data = (new WebserviceService)->getInscricoesturnos($json);
        
        return response(["cursonotfound" => $data['cursonotfound'], "cadeiranotfound" => $data['cadeiranotfound'], "newStudentAdded" => $data['newStudentAdded'], "novasinscricoes" => $data['newDataAdded']], 200);
    }

}
