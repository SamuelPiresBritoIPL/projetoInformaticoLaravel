<?php

namespace App\Services;

use App\Models\Turno;
use App\Models\Cadeira;
use App\Models\Coordenador;
use Illuminate\Support\Facades\Auth;

class CoordenadorService
{

    public function isProfessorCadeira(Cadeira $cadeira){
        $isCoordenador = Coordenador::where('idUtilizador', Auth::user()->id)->where('idCurso', $cadeira->curso->id)->first();
        $isProfCadeira = Cadeira::where('cadeira.id',$cadeira->id)->join('turno','turno.idCadeira','=','cadeira.id')
                        ->join('aula','aula.idTurno','=','turno.id')->where('aula.idProfessor',Auth::user()->id)->first();
        if(empty($isCoordenador) && empty($isProfCadeira)){
            return false;
        }
        return true;
    }

    public function isProfessorTurno(Turno $turno){
        $isCoordenador = Coordenador::where('idUtilizador', Auth::user()->id)->where('idCurso', $turno->cadeira->curso->id)->first();
        $isProfCadeira = Turno::where('turno',$turno->id)
                        ->join('aula','aula.idTurno','=','turno.id')->where('aula.idProfessor',Auth::user()->id)->first();
        if(empty($isCoordenador) && empty($isProfCadeira)){
            return false;
        }
        return true;
    }
}