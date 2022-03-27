<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebserviceController extends Controller
{
    public function getCursos()
    {
    	$json = $this->callAPI("Get","http://www.dei.estg.ipleiria.pt/intranet/horarios/ws/inscricoes/turnos.php?anoletivo=202122&periodo=S2");
        dd($json[0]);
    }

    public function callAPI($method, $url){
        $response = file_get_contents($url);
        $response = json_decode($response);
        return $response;
    }
}
