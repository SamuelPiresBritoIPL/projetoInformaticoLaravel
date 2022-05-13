<?php

namespace App\Http\Controllers;

use App\Models\curso;
use App\Models\Anoletivo;
use App\Models\Coordenador;
use Illuminate\Http\Request;
use App\Services\CursoService;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CursoResource;
use App\Http\Requests\CursoPostRequest;
use App\Http\Resources\CursoResourceCollection;
use Illuminate\Support\Arr;

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
        if(Auth::user()->isEstudante() || Auth::user()->isAdmin()){
            return response(CursoResource::collection(Curso::all()),200);
        }
        if(Auth::user()->isCoordenador()){
            $idsCursos = Coordenador::where('idUtilizador', Auth::user()->id)->pluck('idCurso')->toArray();
            $cursos = Curso::whereIn('id',$idsCursos)->get();
            return response(CursoResource::collection($cursos),200);
        }
        if(Auth::user()->isProfessor()){
            $idsCursos = Coordenador::where('idUtilizador', Auth::user()->id)->pluck('idCurso')->toArray();
            $cursos = Curso::join('cadeira', 'curso.id', '=', 'cadeira.idCurso')->join('turno','turno.idCadeira','=','cadeira.id')
                        ->join('aula','aula.idTurno','=','turno.id')->where('aula.idProfessor',Auth::user()->id)
                        ->select('curso.*')->distinct('curso.id')->get();
            return response(CursoResource::collection($cursos),200);
        }
    	return response(CursoResource::collection(Curso::all()),200);
    }

    public function getCursoComCadeiras(Anoletivo $anoletivo,$semestre)
    {
        if($semestre != 1 && $semestre != 2){
            return response("O semestre não é válido");
        }
        CursoResource::$format = 'cadeira';
    	return response(CursoResourceCollection::make(Curso::all())->anoletivo($anoletivo->id, $semestre),200);
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
        return response(CursoResourceCollection::make($cursos)->anoletivo($anoletivo->id,$semestre),200);
    }

    public function getCoordenadoresByCurso(Curso $curso){
        CursoResource::$format = 'coordenador';
        return response(new CursoResource($curso),200);
    }

    public function getAberturasByCurso(Curso $curso, Anoletivo $anoletivo, $semestre){
        if($semestre != 1 && $semestre != 2){
            return response("O semestre não é válido");
        }
        CursoResource::$format = 'aberturas';
        $curso1 = Curso::where('id',$curso->id)->with(['aberturas' => function ($query) use (&$anoletivo,&$semestre) {
            $query->where('idAnoLetivo', $anoletivo->id)->where('semestre',$semestre);
        }])->first();
        
        return response(CursoResource::make($curso1)->anoletivo($anoletivo->id,$semestre),200);
    }

    public function getCoordenadoresAuth(){
        CursoResource::$format = 'coordenador';
        if(Auth::user()->isCoordenador() || Auth::user()->isProfessor()){
            $idsCursos = Coordenador::where('idUtilizador', Auth::user()->id)->pluck('idCurso')->toArray();
            $cursos = Curso::whereIn('id', $idsCursos)->get();
            return response(CursoResource::collection($cursos),200);
        }
        return response(CursoResource::collection(Curso::all()),200);
    }

    public function getCadeirasByCurso(Curso $curso, Anoletivo $anoletivo, $semestre){
        if($semestre != 1 && $semestre != 2){
            return response("O semestre não é válido");
        }
        CursoResource::$format = 'cadeira';
        return response(CursoResource::make($curso)->anoletivo($anoletivo->id, $semestre),200);
    }
    
}
