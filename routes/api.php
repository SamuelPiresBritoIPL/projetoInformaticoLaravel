<?php

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

Route::get('curso', [CursoController::class, 'index']);
Route::get('cursocadeiras', [CursoController::class, 'getCursoComCadeiras']);



Route::get('webservicecurso', [WebserviceController::class, 'getCursos']);
Route::get('webserviceinscricao', [WebserviceController::class, 'getInscricoesturnos']);
