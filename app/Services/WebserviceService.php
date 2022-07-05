<?php

namespace App\Services;

use Exception;
use App\Models\Aula;
use App\Models\Logs;
use App\Models\Curso;
use App\Models\Turno;
use App\Models\Cadeira;
use App\Models\Anoletivo;
use App\Models\Inscricao;
use App\Models\Utilizador;
use App\Models\Inscricaoucs;
use Illuminate\Support\Facades\DB;

class WebserviceService
{
    public function makeUrl($url,$keys){
        foreach($keys as $key => $value){
            $url = $url . $key . "=" . $value . "&";
        }
        return $url;
    }

    public function callAPI($method, $url){
        try {
            $response = file_get_contents($url);
            if(empty($response)){
                return;
            }
            $respons = json_decode($response);
            return $respons;
        }
        catch (Exception $e) {
            return;
        }
    }

    public function getCursos($json){
        $newDataAdded = 0;
        $updatedData = 0;
        foreach ($json as $turno) {
            $curso = Curso::where('codigo',$turno->CD_Curso)->first();
            if(empty($curso)){
                $curso = new Curso();
                $curso->codigo = $turno->CD_Curso;
                $curso->nome = $turno->NM_CURSO;
                $curso->save();
                $newDataAdded += 1;
            }else{
                $curso->nome = $turno->NM_CURSO;
                $curso->save();
                $updatedData += 1;
            }

            $utilizador = Utilizador::where('login', $turno->LOGIN)->first();
            if(empty($utilizador)){
                $utilizador = new Utilizador();
                $utilizador->nome = $turno->NM_FUNCIONARIO;
                $utilizador->login = $turno->LOGIN;
                $utilizador->idCurso = $curso->id;
                $utilizador->tipo = 1;
                $utilizador->password = "teste123";
                $utilizador->save();
                $newDataAdded += 1;
            }
            
            $cadeira = Cadeira::where('codigo',$turno->CD_Discip)->first();
            if(empty($cadeira)){
                $cadeira = new Cadeira();
                $cadeira->ano = $turno->AnoCurricular;
                $cadeira->codigo = $turno->CD_Discip;
                $cadeira->semestre = str_split($turno->Periodo)[1];
                if($turno->CodDiscipTipo == "TP" || $turno->CodDiscipTipo == "PL"){
                    $cadeira->nome = substr($turno->DS_Discip, 0, -5);
                }else{
                    $cadeira->nome = substr($turno->DS_Discip, 0, -4);
                }
                $cadeira->idCurso = $curso->id;
                $cadeira->save();
                $newDataAdded += 1;
            }else{
                $cadeira->ano = $turno->AnoCurricular;
                $cadeira->semestre = str_split($turno->Periodo)[1];
                if($turno->CodDiscipTipo == "TP" || $turno->CodDiscipTipo == "PL"){
                    $cadeira->nome = substr($turno->DS_Discip, 0, -5);
                }else{
                    $cadeira->nome = substr($turno->DS_Discip, 0, -4);
                }
                $cadeira->idCurso = $curso->id;
                $cadeira->save();
                $newDataAdded += 1;
            }
            
            $anoletivo = Anoletivo::where('anoletivo',$turno->CD_Lectivo)->first();
            if(empty($anoletivo)){
                $anoletivo = new Anoletivo();
                $anoletivo->anoletivo = $turno->CD_Lectivo;
                $anoletivo->save();
            }

            $newturno = Turno::where('idCadeira',$cadeira->id)->where('tipo',$turno->CodDiscipTipo)->where('numero',$turno->CDTurno)->where('idAnoletivo',$anoletivo->id)->first();
            if(empty($newturno)){
                $newturno = new Turno();
                $newturno->idCadeira = $cadeira->id;
                $newturno->idAnoletivo = $anoletivo->id;
                $newturno->tipo = $turno->CodDiscipTipo;
                $newturno->numero = $turno->CDTurno;
                $newturno->save();
                $newDataAdded += 1;
            }
            //antiga maneira de adicionar aulas, agora existe uma api para consumir
            /*$newaula = Aula::where('idTurno',$newturno->id)->where('idProfessor',$utilizador->id)->first();
            if(empty($newaula)){
                $newaula = new Aula();
                $newaula->idTurno = $newturno->id;
                $newaula->idProfessor = $utilizador->id;
                $newaula->save();
                $newDataAdded += 1;
            }*/
        }
        return $newDataAdded;
    }

    public function getInscricoesturnos($json){
        $newStudentAdded = 0;
        $cursonotfound = 0;
        $cadeiranotfound = 0;
        $newDataAdded = 0;
        $dataChanged = 0;
        foreach ($json as $inscricao) {
            $curso = Curso::where('codigo',$inscricao->CD_CURSO)->first();
            if(empty($curso)){
                $cursonotfound += 1;
                continue;
            }

            $cadeira = Cadeira::where('codigo',$inscricao->CD_DISCIP)->first();
            if(empty($cadeira)){
                $cadeiranotfound += 1;
                continue;
            }

            if($inscricao->CD_ALUNO == null){
                $utilizador = Utilizador::where('nome', $inscricao->NM_ALUNO)->where('login', $inscricao->LOGIN)->first();
            }else{
                $utilizador = Utilizador::where('login', $inscricao->CD_ALUNO)->first();
            }
            if(empty($utilizador)){
                $utilizador = new Utilizador();
                $utilizador->nome = $inscricao->NM_ALUNO;
                $utilizador->login = $inscricao->CD_ALUNO;
                $utilizador->idCurso = $curso->id;
                $utilizador->tipo = 0;
                $utilizador->password = "teste123";
                $utilizador->save();
                $newStudentAdded += 1;
            }

            $anoletivo = Anoletivo::where('anoletivo',$inscricao->CD_LECTIVO)->first();
            if(empty($anoletivo)){
                $anoletivo = new Anoletivo();
                $anoletivo->anoletivo = $inscricao->CD_LECTIVO;
                $anoletivo->save();
            }
            
            $inscricaoucs = Inscricaoucs::where('idCadeira', $cadeira->id)->where('idUtilizador', $utilizador->id)->where('idAnoletivo',$anoletivo->id)->first();
            if(empty($inscricaoucs)){
                $newDataAdded += 1;
                $inscricaoucs = new Inscricaoucs();
                $inscricaoucs->idCadeira = $cadeira->id;
                $inscricaoucs->idUtilizador = $utilizador->id;
                $inscricaoucs->idAnoletivo = $anoletivo->id;
                $inscricaoucs->nrinscricoes = $inscricao->NR_INSCRICOES;
            }
            $inscricaoucs->estado = $inscricao->CD_STATUS;
            $inscricaoucs->nrinscricoes = $inscricao->NR_INSCRICOES;
            $inscricaoucs->save();
            $dataChanged += 1;
        }
        return[
            'newStudentAdded' => $newStudentAdded,
            'cursonotfound' => $cursonotfound,
            'cadeiranotfound' => $cadeiranotfound,
            'newDataAdded' => $newDataAdded,
            'dataChanged' => $dataChanged,
        ];
    }

    //fazer esta funcao!!
    public function getAulasJson($json, $idAnoLetivo){
        $newProfessorAdded = 0;
        $turnonotfound = 0;
        $cadeiranotfound = 0;
        $newAula = 0;
        $testes = 0;
        $dataChanged = 0;
        
        foreach ($json as $aula) {
            $cadeira = Cadeira::where('codigo',$aula->cod_uc)->first();
            if(empty($cadeira)){
                $cadeiranotfound += 1;
                continue;
            }

            if($aula->login == null){
                 continue;
            }else{
                $utilizador = Utilizador::where('login', $aula->login)->first();
            }
            if(empty($utilizador)){
                $utilizador = new Utilizador();
                $utilizador->nome = $aula->nome_docente;
                $utilizador->login = $aula->login;
                $utilizador->idCurso = $cadeira->idCurso;
                $utilizador->tipo = 1;
                $utilizador->password = "teste123";
                $utilizador->save();
                $newProfessorAdded += 1;
            }
            
            //estamos presentes num teste
            if($aula->componente == null){
                $testes += 1;
                continue;
            }

            $turnoNr = $aula->turno == "Sem Turno" ? 0 : $aula->turno;
            $turno = Turno::where('tipo', $aula->componente)->where('numero', $turnoNr)->where('idAnoletivo', $idAnoLetivo)->where('idCadeira',$cadeira->id)->first();
            //o turno n existe, sair ou um erro ou n inserir? pensar
            if(empty($turno)){
                $turnonotfound += 1;
                continue;
            }

            //motivo de falta ou algo do genero, comfirmar se e suposto ficar assim!
            if($aula->motivo_falta != null){
                continue;
            }

            $newaula = Aula::where('idAntigo',$aula->id_aulas)->first();
            if(empty($newaula)){
                $newaula = Aula::where('data',date('Y-m-d',strtotime($aula->data)))->where('horaInicio',date('H:i',strtotime($aula->data_inicio)))
                ->where('horaFim',date('H:i',strtotime($aula->data_fim)))->where('idTurno',$turno->id)->where('idProfessor',$utilizador->id)->first();
                if(!empty($newaula)){
                    continue;
                }
            }
            if(empty($newaula)){
                //nova aula
                $newAula += 1;
                $newaula = new Aula();
                $newaula->idAntigo = $aula->id_aulas;
            }
            $newaula->data =  date('Y-m-d',strtotime($aula->data));
            $newaula->horaInicio = date('H:i',strtotime($aula->data_inicio));
            $newaula->horaFim = date('H:i',strtotime($aula->data_fim));
            $newaula->idTurno = $turno->id;
            $newaula->idProfessor = $utilizador->id;
            $newaula->save();
            $dataChanged += 1;
        }
        return[
            'newProfessorAdded' => $newProfessorAdded,
            'turnonotfound' => $turnonotfound,
            'cadeiranotfound' => $cadeiranotfound,
            'dataChanged' => $dataChanged,
            'newAula' => $newAula,
            'testes' => $testes,
        ];
    }

    public function inscreverAlunosTurnosUnicos(Anoletivo $anoletivo, $semestre){
        $turnos = Turno::select('turno.*')->rightjoin('cadeira', function ($join) use(&$semestre) {
            $join->on('turno.idCadeira', '=', 'cadeira.id')->where('cadeira.semestre', '=', $semestre);
        })->where('turno.idAnoletivo','=',$anoletivo->id)->where('turno.numero','=',0)->get();
        $newInsc = 0;
        $failedIns = 0;
        foreach ($turnos as $turno) {
            $ids = Inscricao::where('idTurno',$turno->id)->pluck('idUtilizador')->toArray();
            
            $inscIds = Inscricaoucs::where('inscricaoucs.idCadeira','=',$turno->idCadeira)
                        ->where('inscricaoucs.estado','=',1)->where('inscricaoucs.idAnoletivo','=',$anoletivo->id)
                        ->whereNotIn('inscricaoucs.idUtilizador',$ids)->pluck('idUtilizador')->toArray();
            $data = [];
            foreach($inscIds as $inscId) {
                array_push($data,['idUtilizador' => $inscId, 'idTurno' => $turno->id]);
                
            }
            if(!empty($data)){
                $newInsc_new = Inscricao::insertOrIgnore($data);
                $newInsc += $newInsc_new;
                Turno::where('id', $turno->id)->update(['vagasocupadas' => DB::raw('vagasocupadas+'.$newInsc_new)]);
                $failedIns = count($data) - $newInsc;
            }
        }
        return[
            'novasInscricoes' => $newInsc,
            'inscricoesFalharam' => $failedIns
        ];
    }
}