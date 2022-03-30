<?php

use App\Http\Controllers\CoordenadorController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\WebserviceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//free use
Route::get('curso', [CursoController::class, 'index']);

//admin / coordenador
Route::get('cursocadeiras', [CursoController::class, 'getCursoComCadeiras']);
Route::get('cursocoordenadores',[CursoController::class, 'getCoordenadores']);
Route::get('cursocoordenadores/{curso}',[CursoController::class, 'getCoordenadoresByCurso']);
Route::post('addcoordenador',[CoordenadorController::class, 'store']);
Route::delete('removecoordenador/{coordenador}',[CoordenadorController::class, 'remove']);


Route::get('webservicecurso', [WebserviceController::class, 'getCursos']);
Route::get('webserviceinscricao', [WebserviceController::class, 'getInscricoesturnos']);
