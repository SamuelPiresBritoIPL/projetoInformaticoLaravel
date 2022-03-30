<?php

use App\Http\Controllers\AberturasController;
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
Route::group(['prefix' => 'curso'], function () {
	Route::get('/cadeiras', [CursoController::class, 'getCursoComCadeiras']);
    Route::get('/coordenadores',[CursoController::class, 'getCoordenadores']);
    Route::get('/aberturas',[CursoController::class, 'getAberturas']);
    Route::get('/coordenadores/{curso}',[CursoController::class, 'getCoordenadoresByCurso']);

    Route::post('addabertura/{curso}',[AberturasController::class, 'addAberturas']);
});
Route::post('addcoordenador',[CoordenadorController::class, 'store']);
Route::delete('removecoordenador/{coordenador}',[CoordenadorController::class, 'remove']);



Route::group(['prefix' => 'webservice'], function () {
    Route::get('curso', [WebserviceController::class, 'getCursos']);
    Route::get('inscricao', [WebserviceController::class, 'getInscricoesturnos']);
});

