<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnoletivoPostRequest;
use App\Models\Anoletivo;

class AnoletivoController extends Controller
{
    public function index(){
        return response(Anoletivo::orderby('anoletivo','DESC')->get(),200);
    }

    public function switchAnoletivo(AnoletivoPostRequest $request, Anoletivo $anoletivo){
        $data = collect($request->validated());
        $ativo = Anoletivo::where("ativo", 1)->first();
        
        if(!empty($ativo) && $anoletivo->id != $ativo->id){
            $ativo->ativo = 0;
            $ativo->save();
        }
        $anoletivo->ativo = 1;
        $anoletivo->semestreativo = $data->get('semestre');
        $anoletivo->save();
        return response(200);
    }
}
