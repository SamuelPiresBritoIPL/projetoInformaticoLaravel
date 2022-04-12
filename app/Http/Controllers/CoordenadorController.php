<?php

namespace App\Http\Controllers;

use App\Models\Utilizador;
use App\Models\coordenador;
use Illuminate\Http\Request;
use App\Http\Resources\CoordenadorResource;
use App\Http\Requests\CoordenadorPostRequest;

class CoordenadorController extends Controller
{
    public function index()
    {
    	return response(CoordenadorResource::collection(Coordenador::all()),200);
    }

    public function store(CoordenadorPostRequest $request){
        $data = collect($request->validated());
        if($data->has('login')){
            if(str_contains($data->get('login'), '@')){
                $utilizador = Utilizador::where('email',$data->get('login'))->first();
            }else{
                $utilizador = Utilizador::where('login',$data->get('login'))->first();
            }
        }else{
            $utilizador = Utilizador::where('email',$data->get('email'))->first();
        }
        if(empty($utilizador)){
            return response("Este utilizador não é válido",422);
        }
        $coordenador = Coordenador::where('idUtilizador',$utilizador->id)->where('idCurso',$data->get('idCurso'))->first();
        if(!empty($coordenador)){
            return response("Este utilizador já é administrador da cadeira!",422);
        }
        $coordenador = new Coordenador();
        $coordenador->idUtilizador = $utilizador->id;
        $coordenador->idCurso = $data->get('idCurso');
        $coordenador->tipo = $data->get('tipo');
        $coordenador->save();

        return response(201);
    }

    public function remove(Coordenador $coordenador){
        $coordenador->delete();
        return response(202);
    }
}
