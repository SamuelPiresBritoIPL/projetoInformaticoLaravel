<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Anoletivo;
use App\Http\Requests\AnoletivoPostRequest;
use App\Http\Resources\LogsResource;

class LogsController extends Controller
{
    public function index(){
        $logs = Logs::all();
        return response(LogsResource::collection($logs),200);
    }
}
