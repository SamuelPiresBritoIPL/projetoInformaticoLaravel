<?php

namespace App\Http\Controllers;

use App\Models\curso;
use App\Models\Anoletivo;
use Illuminate\Http\Request;
use App\Services\CursoService;
use App\Http\Resources\CursoResource;
use App\Http\Requests\CursoPostRequest;

class CursoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        CursoResource::$format = 'default';
    	return response(CursoResource::collection(Curso::all()),200);
    }

    public function getCursoComCadeiras()
    {
        CursoResource::$format = 'cadeira';
    	return response(CursoResource::collection(Curso::all()),200);
    }

    public function getCoordenadores(){
        CursoResource::$format = 'coordenador';
        return response(CursoResource::collection(Curso::all()),200);
    }

    public function getAberturas(Anoletivo $anoletivo,$semestre){
        if($semestre != 1 && $semestre != 2){
            return response("O semestre não é válido");
        }
        CursoResource::$format = 'aberturas';
        $cursos = Curso::with(['aberturas' => function ($query) use (&$anoletivo,&$semestre) {
            $query->where('idAnoLetivo', $anoletivo->id)->where('semestre',$semestre);
        }])->get();
        return response(CursoResource::collection($cursos),200);
    }

    public function getCoordenadoresByCurso(Curso $curso){
        CursoResource::$format = 'coordenador';
        return response(new CursoResource($curso),200);
    }

    public function getAberturasByCurso(Curso $curso,Anoletivo $anoletivo,$semestre){
        CursoResource::$format = 'aberturas';
        $curso1 = Curso::where('id',$curso->id)->with(['aberturas' => function ($query) use (&$anoletivo,&$semestre) {
            $query->where('idAnoLetivo', $anoletivo->id)->where('semestre',$semestre);
        }])->first();
        
        return response(new CursoResource($curso1),200);
    }

    public function getCadeirasByCurso(Curso $curso){
        CursoResource::$format = 'cadeira';
        return response(new CursoResource($curso),200);
    }
    
}
