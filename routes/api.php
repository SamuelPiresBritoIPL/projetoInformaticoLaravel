<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\CadeiraController;
use App\Http\Controllers\AberturasController;
use App\Http\Controllers\AnoletivoController;
use App\Http\Controllers\WebserviceController;
use App\Http\Controllers\CoordenadorController;
use App\Http\Controllers\PedidosController;

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
Route::get('anoletivo', [AnoletivoController::class, 'index']);

//admin
Route::put('anoletivo/{anoletivo}', [AnoletivoController::class, 'switchAnoletivo']);

//admin / coordenador
Route::group(['prefix' => 'curso'], function () {
	Route::get('/cadeiras', [CursoController::class, 'getCursoComCadeiras']);
    Route::get('/coordenadores',[CursoController::class, 'getCoordenadores']);
    Route::get('/aberturas/{anoletivo}/{semestre}',[CursoController::class, 'getAberturas']);
    Route::get('/cadeiras/{curso}', [CursoController::class, 'getCadeirasByCurso']);
    Route::get('/aberturas/{curso}/{anoletivo}/{semestre}',[CursoController::class, 'getAberturasByCurso']);
    Route::get('/coordenadores/{curso}',[CursoController::class, 'getCoordenadoresByCurso']);
    Route::get('/pedidos/{curso}/{anoletivo}/{semestre}',[PedidosController::class, 'getPedidosByCurso']);
    Route::put('/pedidos/{pedido}',[PedidosController::class, 'editPedidoByCoordenador']);
});

//admin / coordenador
Route::group(['prefix' => 'abertura'], function () {
    Route::post('/{curso}',[AberturasController::class, 'create']);
    Route::delete('/{abertura}',[AberturasController::class, 'remove']);
    Route::put('/{abertura}',[AberturasController::class, 'update']);
});

//admin / coordenador
Route::group(['prefix' => 'coordenador'], function () {
	Route::post('/',[CoordenadorController::class, 'store']);
    Route::delete('/{coordenador}',[CoordenadorController::class, 'remove']);
});

//aluno
Route::group(['prefix' => 'cadeiras'], function () {
	Route::get('/{utilizador}',[CadeiraController::class, 'getCadeirasUtilizador']);
    Route::get('naoaprovadas/{utilizador}',[CadeiraController::class, 'getCadeirasNaoAprovadasUtilizador']);
    Route::post('pedidos',[PedidosController::class, 'store']);
});

Route::group(['prefix' => 'webservice'], function () {
    Route::post('curso', [WebserviceController::class, 'getCursos']);
    Route::post('inscricao', [WebserviceController::class, 'getInscricoesturnos']);
    Route::post('inscricaoaprovados', [WebserviceController::class, 'getInscricoesturnos2']);
});

