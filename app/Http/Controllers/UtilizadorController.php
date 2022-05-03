<?php

namespace App\Http\Controllers;

use App\Http\Resources\CursoResource;
use App\Models\Utilizador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        CursoResource::$format = 'default';
		return response([
			'login' => $utilizador->login,
            'nome' => $utilizador->nome,
			'tipo' => $utilizador->tipo,
			'access_token' => $token,
			'curso' => new CursoResource($utilizador->curso)
		], 200);
	}
}
