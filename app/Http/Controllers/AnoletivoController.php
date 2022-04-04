<?php

namespace App\Http\Controllers;

use App\Models\Anoletivo;

class AnoletivoController extends Controller
{
    public function index(){
        return response(Anoletivo::all(),200);
    }
}
