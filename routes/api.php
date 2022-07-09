<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\CadeiraController;
use App\Http\Controllers\AberturasController;
use App\Http\Controllers\AnoletivoController;
use App\Http\Controllers\WebserviceController;
use App\Http\Controllers\CoordenadorController;
use App\Http\Controllers\EstudanteController;
use App\Http\Controllers\InscricaoController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\UtilizadorController;

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
Route::post('login', [UtilizadorController::class, 'login'])->name('login');

Route::get('anoletivo', [AnoletivoController::class, 'index']);

//utilizador logado
Route::group(['middleware' => ['auth:api'],'prefix' => 'utilizadorlogado'], function () {
	Route::get('/', [UtilizadorController::class, 'getInfoUtilizadorLogado']);
});

//admin
Route::put('anoletivo/{anoletivo}', [AnoletivoController::class, 'switchAnoletivo']);

Route::group(['middleware' => ['auth:api','admin'], 'prefix' => 'logs'], function () {
	Route::get('/', [LogsController::class, 'index']);
});

Route::group(['middleware' => ['auth:api','estudante'], 'prefix' => 'cursoauth'], function () {
	Route::get('/', [CursoController::class, 'index']);
    Route::get('/coordenadores',[CursoController::class, 'getCoordenadoresAuth']);
    Route::get('/cadeiras/{curso}/{anoletivo}/{semestre}', [CursoController::class, 'getCadeirasByCurso']);
});

//admin / coordenador
Route::group(['middleware' => ['auth:api','coordenador'], 'prefix' => 'curso'], function () {
    Route::get('/cadeiras/{anoletivo}/{semestre}', [CursoController::class, 'getCursoComCadeiras']);
    Route::get('/coordenadores',[CursoController::class, 'getCoordenadores']);
    Route::get('/aberturas/{anoletivo}/{semestre}',[CursoController::class, 'getAberturas']);
    Route::get('/cadeiras/{curso}/{anoletivo}/{semestre}', [CursoController::class, 'getCadeirasByCurso']);
    Route::get('/aberturas/{curso}/{anoletivo}/{semestre}',[CursoController::class, 'getAberturasByCurso']);
    Route::get('/coordenadores/{curso}',[CursoController::class, 'getCoordenadoresByCurso']);
    Route::get('/pedidos/{curso}/{anoletivo}/{semestre}',[PedidosController::class, 'getPedidosByCurso']);
    Route::put('/pedidos/{pedido}',[PedidosController::class, 'editPedidoByCoordenador']);
    Route::put('/turnosvagas/{curso}/{anoletivo}/{semestre}',[CursoController::class, 'editVagasTurnos']);
});

//admin / coordenador ABERTURAS
Route::group(['middleware' => ['auth:api','coordenador'],'prefix' => 'abertura'], function () {
    Route::post('/{curso}',[AberturasController::class, 'create']);
    Route::delete('/{abertura}',[AberturasController::class, 'remove']);
    Route::put('/{abertura}',[AberturasController::class, 'update']);
});

//admin / coordenador
Route::group(['middleware' => ['auth:api','coordenador'],'prefix' => 'coordenador'], function () {
	Route::post('/',[CoordenadorController::class, 'store']);
    Route::delete('/{coordenador}',[CoordenadorController::class, 'remove']);
});

//admin
Route::group(['middleware' => ['auth:api','coordenador'],'prefix' => 'cadeiras'], function () {
	Route::get('/{cadeira}/{anoletivo}',[CadeiraController::class, 'getCadeira']);
	Route::get('stats/{cadeira}/{anoletivo}',[CadeiraController::class, 'getInformacoesCadeira']);
    Route::post('/addaluno/{cadeira}',[CadeiraController::class, 'addAluno']);
    Route::post('/addalunoturno/{turno}',[CadeiraController::class, 'addAlunoTurno']);
    Route::post('/turnosinvisivel/{cadeira}/{anoletivo}/{visivel}',[CadeiraController::class, 'tornarInvisivel']);
    Route::put('/turnovagas/{cadeira}/{anoletivo}',[CadeiraController::class, 'editVagasTurnos']);
    Route::put('mudarturno/{turno}',[CadeiraController::class, 'mudarTurno']);
});

//admin coordenador
Route::group(['middleware' => ['auth:api','estudante'],'prefix' => 'turno'], function () {
	Route::get('stats/{turno}',[TurnoController::class, 'getInformacoesTurnos']);
	Route::put('/{turno}',[TurnoController::class, 'editTurno']);
	Route::get('export/{turno}',[TurnoController::class, 'exportTurno']);
});

//aluno
Route::group(['middleware' => ['auth:api','estudante'],'prefix' => 'cadeirasaluno'], function () {
	Route::get('utilizador',[CadeiraController::class, 'getCadeirasUtilizador']);
    Route::get('infoperiodos',[AberturasController::class, 'getInfoPeriodos']);
    Route::get('confirmar/utilizador',[CadeiraController::class, 'getCadeirasUtilizadorConfirmar']);
    Route::get('turnos/utilizador',[CadeiraController::class, 'getCadeirasTurnosUtilizador']);
    Route::get('naoaprovadas/{utilizador}',[CadeiraController::class, 'getCadeirasNaoAprovadasUtilizador']);
    Route::post('pedidos',[PedidosController::class, 'store']);
    Route::post('inscricao',[InscricaoController::class, 'store']);
    Route::post('sobreposicoes',[InscricaoController::class, 'checkCoincidencias']);
    Route::delete('inscricao/{inscricao}',[InscricaoController::class, 'delete']);
});

//admin
Route::group(['middleware' => ['auth:api','admin'],'prefix' => 'webservice'], function () {
    Route::post('curso', [WebserviceController::class, 'getCursos']);
    Route::post('inscricaotodas', [WebserviceController::class, 'getInscricoesturnosRemakeAll']);//substituir a inscricao e inscricaoaprovados
    Route::post('inscricao', [WebserviceController::class, 'getInscricoesturnos']);//fora de uso mas fica aqui just in case
    Route::post('inscricaoaprovados', [WebserviceController::class, 'getInscricoesturnos2']);//fora de uso mas fica aqui just in case
    Route::post('aulas', [WebserviceController::class, 'getAulas']);
    Route::put('url', [WebserviceController::class, 'changeurl']);
    Route::get('url', [WebserviceController::class, 'geturls']);
    Route::post('inscriverturnos', [WebserviceController::class, 'inscreverTurnos']);
});

//admin
Route::group(['middleware' => ['auth:api','admin'],'prefix' => 'admin'], function () {
    Route::put('changepassword', [UtilizadorController::class, 'changePassword']);
});

//admin/coordenador
Route::group(['middleware' => ['auth:api','coordenador'],'prefix' => 'estudante'], function () {
    Route::get('dados/{estudante}/{anoletivo}/{semestre}', [EstudanteController::class, 'getDados']);
});

//admin
Route::group(['middleware' => ['auth:api','professor'],'prefix' => 'cadeirasprofessor'], function () {
    Route::get('export/{cadeira}',[CadeiraController::class, 'exportCadeira']);
    Route::get('/{anoletivo}/{semestre}', [CadeiraController::class, 'getCadeirasProfessor']);
    Route::get('stats/{cadeira}/{anoletivo}', [CadeiraController::class, 'getStatsCadeiraProfessor']);
    Route::get('cadeira/{cadeira}/{anoletivo}', [CadeiraController::class, 'getCadeiraProfessor']); 
});

Route::group(['middleware' => ['auth:api','professor'],'prefix' => 'turnosprofessor'], function () {
    Route::get('stats/{turno}', [TurnoController::class, 'getInformacoesTurnosProfessores']);
});

