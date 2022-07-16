<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Curso;
use App\Models\Turno;
use App\Models\Anoletivo;
use App\Models\Coordenador;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Services\CursoService;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CursoResource;
use App\Http\Requests\CursoPostRequest;
use App\Http\Resources\CursoResourceCollection;
use App\Models\Aberturas;

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

        CursoResource::$format = 'aberturasDashboard';

        if (Auth::user()->isAdmin()) {
            $now = Carbon::now('Europe/Lisbon');
            $cursos = Curso::with(['aberturas' => function ($query) use (&$anoletivo,&$semestre) {
                $query->where('idAnoLetivo', $anoletivo->id)->where('semestre',$semestre);
            }])->join('aberturas','curso.id','=','aberturas.idCurso')->where('aberturas.dataEncerar', '>=', $now)
            ->whereNull('deleted_at')->select('curso.*')->distinct('curso.id')->get();
        }
        if (Auth::user()->isCoordenador()) {
            $now = Carbon::now('Europe/Lisbon');
            $cursos = Curso::with(['aberturas' => function ($query) use (&$anoletivo,&$semestre) {
                $query->where('idAnoLetivo', $anoletivo->id)->where('semestre',$semestre);
            }])->join('coordenador','curso.id','=','coordenador.idCurso')->where('coordenador.idUtilizador', Auth::user()->id)->select('curso.*')->distinct('curso.id')->get();
        }
                
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

        $aberturas = Aberturas::withTrashed()->whereNotNull('deleted_at')->where('idCurso', '=', $curso->id)
                            ->where('idAnoLetivo', $anoletivo->id)->where('semestre',$semestre)->orderBy('dataEncerar', 'DESC')->get();
        
        return response(["aberturasAtivas" => CursoResource::make($curso1)->anoletivo($anoletivo->id,$semestre), "aberturasDeleted" => $aberturas],200);
    }

    public function getCoordenadoresAuth(){
        CursoResource::$format = 'coordenador';
        if(Auth::user()->isCoordenador() || Auth::user()->isProfessor()){
            $idsCursos = Coordenador::where('idUtilizador', Auth::user()->id)->where('tipo', 0)->pluck('idCurso')->toArray();
            $cursos = Curso::whereIn('id', $idsCursos)->get();
            return response(CursoResource::collection($cursos),200);
        }
        return response(CursoResource::collection(Curso::all()),200);
    }

    public function getCadeirasByCurso(Curso $curso, Anoletivo $anoletivo, $semestre){
        if($semestre != 1 && $semestre != 2){
            return response("O semestre não é válido");
        }
        $tiposturnos = Turno::join('cadeira','cadeira.id','turno.idCadeira')->where('cadeira.idCurso',$curso->id)
                            ->where('cadeira.semestre',$semestre)->where('turno.idAnoletivo',$anoletivo->id)
                            ->select("turno.tipo")->groupby("tipo")->pluck('tipo')->toArray();
        CursoResource::$format = 'cadeira';
        return response(["cadeiras" => CursoResource::make($curso)->anoletivo($anoletivo->id, $semestre),"tiposTurnos" => $tiposturnos],200);
    }

    public function editVagasTurnos(Request $request,Curso $curso, Anoletivo $anoletivo, $semestre){
        if($semestre != 1 && $semestre != 2){
            return response("O semestre não é válido");
        }
        if($request->has("tipoturno") && $request->has("vagas")){
            for ($i = 0; $i < count($request->get("tipoturno")); $i++) {
                if($request->get('vagas')[$i] != null && $request->get('vagas')[$i] > 0){
                    Turno::join('cadeira','cadeira.id','turno.idCadeira')->where('cadeira.idCurso',$curso->id)
                            ->where('cadeira.semestre',$semestre)->where('turno.idAnoletivo',$anoletivo->id)
                            ->where('turno.tipo', $request->get("tipoturno")[$i])
                            ->update(['turno.vagastotal' => ($request->get('vagas')[$i])]);
                }
            }
        }
        return response("Atualizações efetuadas com sucesso!", 200);
    }
    
}
