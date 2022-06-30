<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Turno;
use App\Models\Cadeira;
use App\Models\Anoletivo;
use App\Models\Utilizador;
use App\Models\Inscricaoucs;
use Illuminate\Http\Request;
use App\Services\LogsService;
use App\Services\WebserviceService;
use Illuminate\Support\Facades\Auth;
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
        }else{
            (new LogsService)->save("Atualização dos turnos feita por: " . Auth::user()->login, "webservices",  Auth::user()->id);
        }

        $newDataAdded = (new WebserviceService)->getCursos($json);
        
        return response($newDataAdded, 200);
    }

    public function getInscricoesturnos2(WebservicePostRequest $request){
        return $this->getInscricoesturnos($request, 2);
    }

    public function getInscricoesturnos(WebservicePostRequest $request, $estado=1){
        $data = collect($request->validated());

        $idcurso = 0;
        $curso = Curso::find($idcurso);
        if($request->has('idcurso')){
            $idcurso = $request->get('idcurso');
            if($idcurso != 0){
                $curso = Curso::find($idcurso);
                if(empty($curso)){
                    return response("O curso não foi encontrado", 400);
                }
                $idcurso = $curso->codigo;
            }
        }

        set_time_limit(750);
        $baseurl = "";
        if (Storage::disk('local')->exists('urlinscricoes.txt')) {
            $baseurl = Storage::disk('local')->get('urlinscricoes.txt');
        }else{
            Storage::disk('local')->put("urlinscricoes.txt", config('services.webapiurls.turnos'));
            $baseurl = Storage::disk('local')->get('urlinscricoes.txt');
        }

        $url = (new WebserviceService)->makeUrl($baseurl,['anoletivo' => $data->get('anoletivo'),'estado' => $estado]); //cod_curso=9119
        
        $data = ["cursonotfound" => 0,"cadeiranotfound" => 0,"newStudentAdded" => 0,"newDataAdded" => 0,'dataChanged' => 0];
        if($idcurso == 0){
            $cursos = Curso::all();
            foreach ($cursos as $c) {
                $url2 = $url . "cod_curso=" . $c->codigo;
                $dataSing = (new WebserviceController)->makeFinalUrlAndData($url2);
                if(!empty($dataSing)){
                    $data["cursonotfound"] += $dataSing["cursonotfound"];
                    $data["cadeiranotfound"] += $dataSing["cadeiranotfound"];
                    $data["newStudentAdded"] += $dataSing["newStudentAdded"];
                    $data["newDataAdded"] += $dataSing["newDataAdded"];
                    $data["dataChanged"] += $dataSing["dataChanged"];
                }
            }
            (new LogsService)->save("Atualização das inscricoes de todos os cursos feita por: " . Auth::user()->login, "webservices",  Auth::user()->id);
        }else{
            $url = $url . "cod_curso=" . $idcurso;
            $json = (new WebserviceService)->callAPI("GET",$url);
            if(empty($json)){
                return response("Não foi possivel aceder ao website", 401);
            }
            $data = (new WebserviceService)->getInscricoesturnos($json);

            (new LogsService)->save("Atualização das inscricoes feita por: " . Auth::user()->login ." ao curso " . $curso->nome, "webservices",  Auth::user()->id);
        }

        return response(["cursonotfound" => $data['cursonotfound'], "cadeiranotfound" => $data['cadeiranotfound'], "newStudentAdded" => $data['newStudentAdded'], "novasinscricoes" => $data['newDataAdded'],"dataChanged" => $data['dataChanged']], 200);
    }

    public function getAulas(WebservicePostRequest $request, $estado=1){
        $data = collect($request->validated());

        $idcurso = 0;
        $curso = Curso::find($idcurso);
        if($request->has('idcurso')){
            $idcurso = $request->get('idcurso');
            if($idcurso != 0){
                $curso = Curso::find($idcurso);
                if(empty($curso)){
                    return response("O curso não foi encontrado", 400);
                }
                $idcurso = $curso->codigo;
            }
        }

        set_time_limit(750);
        $baseurl = "";
        if (Storage::disk('local')->exists('urlaulas.txt')) {
            $baseurl = Storage::disk('local')->get('urlaulas.txt');
        }else{
            Storage::disk('local')->put("urlaulas.txt", config('services.webapiurls.aulas'));
            $baseurl = Storage::disk('local')->get('urlaulas.txt');
        }

        $url = (new WebserviceService)->makeUrl($baseurl,['anoletivo' => $data->get('anoletivo'),'estado' => $estado]); //cod_curso=9119
        
        $data = ["cursonotfound" => 0,"cadeiranotfound" => 0,"newStudentAdded" => 0,"newDataAdded" => 0,'dataChanged' => 0];
        if($idcurso == 0){
            $cursos = Curso::all();
            foreach ($cursos as $c) {
                $url2 = $url . "cod_curso=" . $c->codigo;
                $dataSing = (new WebserviceController)->makeFinalUrlAndData($url2);
                if(!empty($dataSing)){
                    $data["cursonotfound"] += $dataSing["cursonotfound"];
                    $data["cadeiranotfound"] += $dataSing["cadeiranotfound"];
                    $data["newStudentAdded"] += $dataSing["newStudentAdded"];
                    $data["newDataAdded"] += $dataSing["newDataAdded"];
                    $data["dataChanged"] += $dataSing["dataChanged"];
                }
            }
            (new LogsService)->save("Atualização das inscricoes de todos os cursos feita por: " . Auth::user()->login, "webservices",  Auth::user()->id);
        }else{
            $url = $url . "cod_curso=" . $idcurso;
            $json = (new WebserviceService)->callAPI("GET",$url);
            if(empty($json)){
                return response("Não foi possivel aceder ao website", 401);
            }
            $data = (new WebserviceService)->getInscricoesturnos($json);

            (new LogsService)->save("Atualização das inscricoes feita por: " . Auth::user()->login ." ao curso " . $curso->nome, "webservices",  Auth::user()->id);
        }

        return response(["cursonotfound" => $data['cursonotfound'], "cadeiranotfound" => $data['cadeiranotfound'], "newStudentAdded" => $data['newStudentAdded'], "novasinscricoes" => $data['newDataAdded'],"dataChanged" => $data['dataChanged']], 200);
    }

    public function makeFinalUrlAndData($url){
        $json = (new WebserviceService)->callAPI("GET",$url);
        if(empty($json)){
            return;
        }
        $data = (new WebserviceService)->getInscricoesturnos($json);
        return $data;
    }

    public function changeurl(Request $request){
        if($request->has('urlturnos')){
            Storage::disk('local')->put("urlcursos.txt", $request->get('urlturnos'));
            (new LogsService)->save("Atualização do url 'urlcursos' feita por: " . Auth::user()->login, "webservices",  Auth::user()->id);
        }
        if($request->has('urlinscricoes')){
            Storage::disk('local')->put("urlinscricoes.txt", $request->get('urlinscricoes'));
            (new LogsService)->save("Atualização do url 'urlinscricoes' feita por: " . Auth::user()->login, "webservices",  Auth::user()->id);
        }
        if($request->has('urlaulas')){
            Storage::disk('local')->put("urlaulas.txt", $request->get('urlaulas'));
            (new LogsService)->save("Atualização do url 'urlaulas' feita por: " . Auth::user()->login, "webservices",  Auth::user()->id);
        }
        return response(200);
    }

    public function geturls(){
        if (!Storage::disk('local')->exists('urlinscricoes.txt')) {
            Storage::disk('local')->put("urlinscricoes.txt", config('services.webapiurls.turnos'));
        }
        if (!Storage::disk('local')->exists('urlcursos.txt')) {
            Storage::disk('local')->put("urlcursos.txt", config('services.webapiurls.cursos'));
        }
        if (!Storage::disk('local')->exists('urlaulas.txt')) {
            Storage::disk('local')->put("urlaulas.txt", config('services.webapiurls.aulas'));
        }
        $baseurl1 = Storage::disk('local')->get('urlinscricoes.txt');
        $baseurl2 = Storage::disk('local')->get('urlcursos.txt');
        $baseurl3 = Storage::disk('local')->get('urlaulas.txt');
        return response(["urlturnos" => $baseurl2,"urlinscricoes" => $baseurl1,"urlaulas" => $baseurl3],200);
    }

    public function inscreverTurnos(WebservicePostRequest $request)
    {
        $data = collect($request->validated());
        if(empty($data->get('semestre'))){
            return response("O semestre deve ser indicado para esta pedido", 401);
        }
        $anoletivo = Anoletivo::where('id', $data->get('anoletivo'))->first();
        if(empty($anoletivo)){
            return response("O ano letivo selecionado não foi encontrado", 401);
        }
        $newData = (new WebserviceService)->inscreverAlunosTurnosUnicos($anoletivo, $data->get('semestre'));
        (new LogsService)->save("Inscricao em todos os turnos unicos feita por: " . Auth::user()->login, "webservices",  Auth::user()->id);
        return response($newData, 200);
    }
}
