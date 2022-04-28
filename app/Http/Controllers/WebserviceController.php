<?php

namespace App\Http\Controllers;

use App\Models\curso;
use App\Models\turno;
use App\Models\cadeira;
use App\Models\utilizador;
use App\Models\inscricaoucs;
use Illuminate\Http\Request;
use App\Services\WebserviceService;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\WebservicePostRequest;

class WebserviceController extends Controller
{
    public function getCursos(WebservicePostRequest $request)
    {
        $data = collect($request->validated());
        if(empty($data->get('semestre'))){
            return response("O semestre deve ser indicado para esta pedido", 401);
        }

        $baseurl = "";
        if (Storage::disk('local')->exists('urlcursos.txt')) {
            $baseurl = Storage::disk('local')->get('urlcursos.txt');
        }else{
            Storage::disk('local')->put("urlcursos.txt", config('services.webapiurls.cursos'));
            $baseurl = Storage::disk('local')->get('urlcursos.txt');
        }

        $url = (new WebserviceService)->makeUrl($baseurl,['anoletivo' => $data->get('anoletivo'),'periodo' => 'S'.$data->get('semestre')]);
        
    	$json = (new WebserviceService)->callAPI("GET",$url);
        if(empty($json)){
            return response("Não foi possivel aceder ao website", 401);
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
        $baseurl = "";
        if (Storage::disk('local')->exists('urlinscricoes.txt')) {
            $baseurl = Storage::disk('local')->get('urlinscricoes.txt');
        }else{
            Storage::disk('local')->put("urlinscricoes.txt", config('services.webapiurls.turnos'));
            $baseurl = Storage::disk('local')->get('urlinscricoes.txt');
        }

        $url = (new WebserviceService)->makeUrl($baseurl,['anoletivo' => $data->get('anoletivo'),'estado' => $estado]);
        
    	$json = (new WebserviceService)->callAPI("GET",$url);

        if(empty($json)){
            return response("Não foi possivel aceder ao website", 401);
        }

        $data = (new WebserviceService)->getInscricoesturnos($json);
        
        return response(["cursonotfound" => $data['cursonotfound'], "cadeiranotfound" => $data['cadeiranotfound'], "newStudentAdded" => $data['newStudentAdded'], "novasinscricoes" => $data['newDataAdded']], 200);
    }

    public function changeurl(Request $request){
        if($request->has('urlturnos')){
            Storage::disk('local')->put("urlcursos.txt", $request->get('urlturnos'));
        }
        if($request->has('urlinscricoes')){
            Storage::disk('local')->put("urlinscricoes.txt", $request->get('urlinscricoes'));
        }
        return response(200);
    }

    public function geturls(){
        $baseurl1 = Storage::disk('local')->get('urlinscricoes.txt');
        $baseurl2 = Storage::disk('local')->get('urlcursos.txt');
        return response(["urlturnos" => $baseurl2,"urlinscricoes" => $baseurl1],200);
    }
}
