<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Anoletivo;
use App\Http\Requests\AnoletivoPostRequest;
use App\Http\Resources\LogsResource;

class LogsController extends Controller
{
    public function index(){
        $logs = Logs::orderBy('created_at','DESC')->get();
        return response(LogsResource::collection($logs),200);
    }
}
