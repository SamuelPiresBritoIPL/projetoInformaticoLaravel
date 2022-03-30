<?php

namespace App\Http\Controllers;

use App\Models\curso;
use Illuminate\Http\Request;
use App\Http\Resources\CursoResource;

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

    public function getAberturas(){
        CursoResource::$format = 'aberturas';
        return response(CursoResource::collection(Curso::all()),200);
    }

    public function getCoordenadoresByCurso(Curso $curso){
        CursoResource::$format = 'coordenador';
        return response(new CursoResource($curso),200);
    }
    
}
