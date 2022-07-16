<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Utilizador;
use App\Models\Coordenador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\CursoResource;
use App\Http\Resources\UtilizadorResource;

class UtilizadorController extends Controller
{
    public function login(Request $request)
	{
		//check phone number
		$utilizador = Utilizador::where('login', $request->login)->first();
		if (!$utilizador) {
			return response([
				'message' => 'O login não foi encontrado'
			], 401);
		}
		if (!Hash::check(($request->password), $utilizador->password)) {
			return response([
				'message' => 'password Incorrect'
			], 401);
		}
		//delete previous tokens
		$utilizador->tokens()->delete();
		$token = $utilizador->createToken('authToken')->accessToken;
		$aulas = Aula::where('idProfessor', $utilizador->id)->first();
		$idsCursos = Coordenador::where('idUtilizador', $utilizador->id)->where('tipo', 0)->pluck('idCurso')->toArray();
        CursoResource::$format = 'default';
		return response([
			'login' => $utilizador->login,
            'nome' => $utilizador->nome,
			'tipo' => $utilizador->tipo,
			'isCoordenador' => $utilizador->isCoordenador() ? 1 : 0,
			'isCoordenadorPrincipal' => ($utilizador->isCoordenador() ? (count($idsCursos) > 0 ? 1 : 0): 0),
			'isProfessor' => empty($aulas) ? 0 : 1,
			'access_token' => $token,
			'curso' => (!empty($utilizador->curso)) ? new CursoResource($utilizador->curso) : ""
		], 200);
	}

	public function getInfoUtilizadorLogado(Request $request){
		return new UtilizadorResource($request->user());
	}

	public function changePassword(Request $request){
		if(!$request->has("password")){
			return response([
				'message' => 'Falta a password.'
			], 401);
		}
		if(!$request->has("newpassword")){
			return response([
				'message' => 'Falta a newpassword'
			], 401);
		}
		if (!Hash::check(($request->password), Auth::user()->password)) {
			return response([
				'message' => 'Password Incorrect'
			], 401);
		}

		$admin = Utilizador::where('id',Auth::user()->id)->first();
		$admin->password = $request->get("newpassword");
		$admin->save();
		
		return response(201);
	}
}
