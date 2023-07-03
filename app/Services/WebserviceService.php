<?php

namespace App\Services;

use Exception;
use App\Models\Aula;
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
    //global variables
    private $globalPassword = "123";


    public function makeUrl($url, $keys)
    {
        foreach ($keys as $key => $value) {
            $url = $url . $key . "=" . $value . "&";
        }
        return $url;
    }

    public function callAPI($method, $url)
    {
        try {
            $response = file_get_contents($url);
            if (empty($response)) {
                return;
            }
            $respons = json_decode($response);
            return $respons;
        } catch (Exception $e) {
            return;
        }
    }

    public function getCursos($json)
    {
        $newDataAdded = 0;
        $updatedData = 0;
        foreach ($json as $turno) {
            $curso = Curso::where('codigo', $turno->CD_Curso)->first();
            if (empty($curso)) {
                $curso = new Curso();
                $curso->codigo = $turno->CD_Curso;
                $curso->nome = $turno->NM_CURSO;
                $curso->save();
                $newDataAdded += 1;
            } else {
                $curso->nome = $turno->NM_CURSO;
                $curso->save();
                $updatedData += 1;
            }

            $utilizador = Utilizador::where('login', $turno->LOGIN)->first();
            if (empty($utilizador)) {
                $utilizador = new Utilizador();
                $utilizador->nome = $turno->NM_FUNCIONARIO;
                $utilizador->login = $turno->LOGIN;
                $utilizador->idCurso = $curso->id;
                $utilizador->tipo = 1;
                $utilizador->password = $this->globalPassword;
                $utilizador->save();
                $newDataAdded += 1;
            }

            $cadeira = Cadeira::where('codigo', $turno->CD_Discip)->first();
            if (empty($cadeira)) {
                $cadeira = new Cadeira();
                $cadeira->ano = $turno->AnoCurricular;
                $cadeira->codigo = $turno->CD_Discip;
                $cadeira->semestre = str_split($turno->Periodo)[1];
                if ($turno->CodDiscipTipo == "TP" || $turno->CodDiscipTipo == "PL") {
                    $cadeira->nome = substr($turno->DS_Discip, 0, -5);
                } else {
                    $cadeira->nome = substr($turno->DS_Discip, 0, -4);
                }
                $cadeira->idCurso = $curso->id;
                $cadeira->save();
                $newDataAdded += 1;
            } else {
                $cadeira->ano = $turno->AnoCurricular;
                $cadeira->semestre = str_split($turno->Periodo)[1];
                if ($turno->CodDiscipTipo == "TP" || $turno->CodDiscipTipo == "PL") {
                    $cadeira->nome = substr($turno->DS_Discip, 0, -5);
                } else {
                    $cadeira->nome = substr($turno->DS_Discip, 0, -4);
                }
                $cadeira->idCurso = $curso->id;
                $cadeira->save();
                $newDataAdded += 1;
            }

            $anoletivo = Anoletivo::where('anoletivo', $turno->CD_Lectivo)->first();
            if (empty($anoletivo)) {
                $anoletivo = new Anoletivo();
                $anoletivo->anoletivo = $turno->CD_Lectivo;
                $anoletivo->save();
            }

            $newturno = Turno::where('idCadeira', $cadeira->id)->where('tipo', $turno->CodDiscipTipo)->where('numero', $turno->CDTurno)->where('idAnoletivo', $anoletivo->id)->first();
            if (empty($newturno)) {
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

    public function getCursosCSV($turno)
    {
        $newDataAdded = 0;
        $updatedData = 0;
        $turno = $turno->toArray();



        // dd($turno[0]['CD_Curso']);
        for ($i = 0; $i < count($turno); $i++) {

            $curso = Curso::where('codigo', $turno[$i]['CD_Curso'])->first();
            if (empty($curso)) {
                $curso = new Curso();
                $curso->codigo = $turno[$i]['CD_Curso'];
                $curso->nome = $turno[$i]['NM_CURSO'];
                $curso->save();
                $newDataAdded += 1;
            } else {
                $curso->nome = $turno[$i]['NM_CURSO'];
                $curso->save();
                $updatedData += 1;
            }

            $turno[$i]['LOGIN'] = $turno[$i]['LOGIN'] == "null" || $turno[$i]['LOGIN'] == null ? null : $turno[$i]['LOGIN'];
            if ($turno[$i]['LOGIN']) {
                $utilizador = Utilizador::where('login', $turno[$i]['LOGIN'])->first();
                if (empty($utilizador)) {
                    $utilizador = new Utilizador();
                    $utilizador->nome = $turno[$i]['NM_FUNCIONARIO'];
                    $utilizador->login = $turno[$i]['LOGIN'];
                    $utilizador->idCurso = $curso->id;
                    $utilizador->tipo = 1;
                    $utilizador->password = $this->globalPassword;
                    $utilizador->save();
                    $newDataAdded += 1;
                }
            }


            $turno[$i]['CD_Discip'] = $turno[$i]['CD_Discip'] == "null" || $turno[$i]['CD_Discip'] == null ? null : $turno[$i]['CD_Discip'];
            if ($turno[$i]['CD_Discip']) {
                $cadeira = Cadeira::where('codigo', $turno[$i]['CD_Discip'])->first();
                if (empty($cadeira)) {
                    $cadeira = new Cadeira();
                    $cadeira->ano = $turno[$i]['AnoCurricular'];
                    $cadeira->codigo = $turno[$i]['CD_Discip'];
                    $cadeira->semestre = str_split($turno[$i]['Periodo'])[1];
                    if ($turno[$i]['CodDiscipTipo'] == "TP" || $turno[$i]['CodDiscipTipo'] == "PL") {
                        $cadeira->nome = substr($turno[$i]['DS_Discip'], 0, -5);
                    } else {
                        $cadeira->nome = substr($turno[$i]['DS_Discip'], 0, -4);
                    }
                    $cadeira->idCurso = $curso->id;
                    $cadeira->save();
                    $newDataAdded += 1;
                } else {
                    $cadeira->ano = $turno[$i]['AnoCurricular'];
                    $cadeira->semestre = str_split($turno[$i]['Periodo'])[1];
                    if ($turno[$i]['CodDiscipTipo'] == "TP" || $turno[$i]['CodDiscipTipo'] == "PL") {
                        $cadeira->nome = substr($turno[$i]['DS_Discip'], 0, -5);
                    } else {
                        $cadeira->nome = substr($turno[$i]['DS_Discip'], 0, -4);
                    }
                    $cadeira->nome = trim($cadeira->nome);
                    $cadeira->idCurso = $curso->id;
                    $cadeira->save();
                    $newDataAdded += 1;
                }
            }

            $turno[$i]['CD_Lectivo'] = $turno[$i]['CD_Lectivo'] == "null" || $turno[$i]['CD_Lectivo'] == null ? null : $turno[$i]['CD_Lectivo'];
            if ($turno[$i]['CD_Lectivo']) {
                $anoletivo = Anoletivo::where('anoletivo', $turno[$i]['CD_Lectivo'])->first();
                if (empty($anoletivo)) {
                    $anoletivo = new Anoletivo();
                    $anoletivo->anoletivo = $turno[$i]['CD_Lectivo'];
                    $anoletivo->save();
                }
            }

            $turno[$i]['CodDiscipTipo'] = $turno[$i]['CodDiscipTipo'] == "null" || $turno[$i]['CodDiscipTipo'] == null ? null : $turno[$i]['CodDiscipTipo'];
            if ($turno[$i]['CodDiscipTipo']) {
                $newturno = Turno::where('idCadeira', $cadeira->id)->where('tipo', $turno[$i]['CodDiscipTipo'])->where('numero', $turno[$i]['CDTurno'])->where('idAnoletivo', $anoletivo->id)->first();
                if (empty($newturno)) {
                    $newturno = new Turno();
                    $newturno->idCadeira = $cadeira->id;
                    $newturno->idAnoletivo = $anoletivo->id;
                    $newturno->tipo = $turno[$i]['CodDiscipTipo'];
                    $newturno->numero = $turno[$i]['CDTurno'];
                    $newturno->save();
                    $newDataAdded += 1;
                }
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

    public function getInscricoesturnos($json)
    {
        $newStudentAdded = 0;
        $cursonotfound = 0;
        $cadeiranotfound = 0;
        $newDataAdded = 0;
        $dataChanged = 0;
        foreach ($json as $inscricao) {
            $curso = Curso::where('codigo', $inscricao->CD_CURSO)->first();
            if (empty($curso)) {
                $cursonotfound += 1;
                continue;
            }

            $cadeira = Cadeira::where('codigo', $inscricao->CD_DISCIP)->first();
            if (empty($cadeira)) {
                $cadeiranotfound += 1;
                continue;
            }

            if ($inscricao->CD_ALUNO == null) {
                $utilizador = Utilizador::where('nome', $inscricao->NM_ALUNO)->where('login', $inscricao->LOGIN)->first();
            } else {
                $utilizador = Utilizador::where('login', $inscricao->CD_ALUNO)->first();
            }
            if (empty($utilizador)) {
                $utilizador = new Utilizador();
                $utilizador->nome = $inscricao->NM_ALUNO;
                $utilizador->login = $inscricao->CD_ALUNO;
                $utilizador->idCurso = $curso->id;
                $utilizador->tipo = 0;
                $utilizador->password = $this->globalPassword;
                $utilizador->save();
                $newStudentAdded += 1;
            }

            $anoletivo = Anoletivo::where('anoletivo', $inscricao->CD_LECTIVO)->first();
            if (empty($anoletivo)) {
                $anoletivo = new Anoletivo();
                $anoletivo->anoletivo = $inscricao->CD_LECTIVO;
                $anoletivo->save();
            }

            $inscricaoucs = Inscricaoucs::where('idCadeira', $cadeira->id)->where('idUtilizador', $utilizador->id)->where('idAnoletivo', $anoletivo->id)->first();
            if (empty($inscricaoucs)) {
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
        return [
            'newStudentAdded' => $newStudentAdded,
            'cursonotfound' => $cursonotfound,
            'cadeiranotfound' => $cadeiranotfound,
            'newDataAdded' => $newDataAdded,
            'dataChanged' => $dataChanged,
        ];
    }

    public function getInscricoesturnosCSV($request)
    {
        $newStudentAdded = 0;
        $cursonotfound = 0;
        $cadeiranotfound = 0;
        $newDataAdded = 0;
        $dataChanged = 0;
        $inscricao = $request->toArray();


        for ($i = 0; $i < count($inscricao); $i++) {

            $curso = Curso::where('codigo', $inscricao[$i]['CD_CURSO'])->first();
            if (empty($curso)) {
                $cursonotfound += 1;
                continue;
            }

            $cadeira = Cadeira::where('codigo', $inscricao[$i]['CD_DISCIP'])->first();
            if (empty($cadeira)) {
                $cadeiranotfound += 1;
                continue;
            }


            $inscricao[$i]['NM_ALUNO'] = $inscricao[$i]['NM_ALUNO'] == "null" ? null : $inscricao[$i]['NM_ALUNO'];

            if ($inscricao[$i]['CD_ALUNO'] == null) {
                $utilizador = Utilizador::where('nome', $inscricao[$i]['NM_ALUNO'])->where('login', $inscricao[$i]['LOGIN'])->first();
            } else {
                $utilizador = Utilizador::where('login', $inscricao[$i]['CD_ALUNO'])->first();
            }
            if (empty($utilizador)) {
                $utilizador = new Utilizador();
                $utilizador->nome = $inscricao[$i]['NM_ALUNO'];
                $utilizador->login = $inscricao[$i]['CD_ALUNO'];
                $utilizador->idCurso = $curso->id;
                $utilizador->tipo = 0;
                $utilizador->password = $this->globalPassword;
                $utilizador->save();
                $newStudentAdded += 1;
            }

            $anoletivo = Anoletivo::where('anoletivo', $inscricao[$i]['CD_LECTIVO'])->first();
            if (empty($anoletivo)) {
                $anoletivo = new Anoletivo();
                $anoletivo->anoletivo = $inscricao[$i]['CD_LECTIVO'];
                $anoletivo->save();
            }

            $inscricaoucs = Inscricaoucs::where('idCadeira', $cadeira->id)->where('idUtilizador', $utilizador->id)->where('idAnoletivo', $anoletivo->id)->first();
            if (empty($inscricaoucs)) {
                $newDataAdded += 1;
                $inscricaoucs = new Inscricaoucs();
                $inscricaoucs->idCadeira = $cadeira->id;
                $inscricaoucs->idUtilizador = $utilizador->id;
                $inscricaoucs->idAnoletivo = $anoletivo->id;
                $inscricaoucs->nrinscricoes = $inscricao[$i]['NR_INSCRICOES'];
            }
            $inscricaoucs->estado = $inscricao[$i]['CD_STATUS'];
            $inscricaoucs->nrinscricoes = $inscricao[$i]['NR_INSCRICOES'];
            $inscricaoucs->save();
            $dataChanged += 1;
        }

        return [
            'newStudentAdded' => $newStudentAdded,
            'cursonotfound' => $cursonotfound,
            'cadeiranotfound' => $cadeiranotfound,
            'newDataAdded' => $newDataAdded,
            'dataChanged' => $dataChanged,
        ];
    }

    //fazer esta funcao!!
    public function getAulasJson($json, $idAnoLetivo)
    {
        $newProfessorAdded = 0;
        $turnonotfound = 0;
        $cadeiranotfound = 0;
        $newAula = 0;
        $testes = 0;
        $dataChanged = 0;

        foreach ($json as $aula) {
            $cadeira = Cadeira::where('codigo', $aula->cod_uc)->first();
            if (empty($cadeira)) {
                $cadeiranotfound += 1;
                continue;
            }

            if ($aula->login == null) {
                continue;
            } else {
                $utilizador = Utilizador::where('login', $aula->login)->first();
            }
            if (empty($utilizador)) {
                $utilizador = new Utilizador();
                $utilizador->nome = $aula->nome_docente;
                $utilizador->login = $aula->login;
                $utilizador->idCurso = $cadeira->idCurso;
                $utilizador->tipo = 1;
                $utilizador->password = $this->globalPassword;
                $utilizador->save();
                $newProfessorAdded += 1;
            }

            //estamos presentes num teste
            if ($aula->componente == null) {
                $testes += 1;
                continue;
            }

            $turnoNr = $aula->turno == "Sem Turno" ? 0 : $aula->turno;
            $turno = Turno::where('tipo', $aula->componente)->where('numero', $turnoNr)->where('idAnoletivo', $idAnoLetivo)->where('idCadeira', $cadeira->id)->first();
            //o turno n existe, sair ou um erro ou n inserir? pensar
            if (empty($turno)) {
                $turnonotfound += 1;
                continue;
            }

            //motivo de falta ou algo do genero, comfirmar se e suposto ficar assim!
/*            if($aula->motivo_falta != null && $aula->motivo_falta != '&nbsp;'){
                continue;
            }*/

            $newaula = Aula::where('idAntigo', $aula->id_aulas)->first();
            if (empty($newaula)) {
                $newaula = Aula::where('data', date('Y-m-d', strtotime($aula->data)))->where('horaInicio', date('H:i', strtotime($aula->data_inicio)))
                    ->where('horaFim', date('H:i', strtotime($aula->data_fim)))->where('idTurno', $turno->id)->where('idProfessor', $utilizador->id)->first();
                if (!empty($newaula)) {
                    continue;
                }
            }
            if (empty($newaula)) {
                //nova aula
                $newAula += 1;
                $newaula = new Aula();
                $newaula->idAntigo = $aula->id_aulas;
            }
            $newaula->data = date('Y-m-d', strtotime($aula->data));
            $newaula->horaInicio = date('H:i', strtotime($aula->data_inicio));
            $newaula->horaFim = date('H:i', strtotime($aula->data_fim));
            $newaula->idTurno = $turno->id;
            $newaula->idProfessor = $utilizador->id;
            $newaula->save();
            $dataChanged += 1;
        }
        return [
            'newProfessorAdded' => $newProfessorAdded,
            'turnonotfound' => $turnonotfound,
            'cadeiranotfound' => $cadeiranotfound,
            'dataChanged' => $dataChanged,
            'newAula' => $newAula,
            'testes' => $testes,
            'json' => $json
        ];
    }

    public function getAulasCSV($request)
    {
        $newProfessorAdded = 0;
        $turnonotfound = 0;
        $cadeiranotfound = 0;
        $newAula = 0;
        $testes = 0;
        $dataChanged = 0;

        $idAnoLetivo = Anoletivo::where('ativo', 1)->first()->id;

        $aula = $request->toArray();


        for ($i = 0; $i < count($aula); $i++) {

            $cadeira = Cadeira::where('codigo', $aula[$i]['cod_uc'])->first();
            if (empty($cadeira)) {
                $cadeiranotfound += 1;
                continue;
            }

            if ($aula[$i]['login'] == null) {
                continue;
            } else {
                $utilizador = Utilizador::where('login', $aula[$i]['login'])->first();
            }
            if (empty($utilizador)) {
                $utilizador = new Utilizador();
                $utilizador->nome = $aula[$i]['nome_docente'];
                $utilizador->login = $aula[$i]['login'];
                $utilizador->idCurso = $cadeira->idCurso;
                $utilizador->tipo = 1;
                $utilizador->password = $this->globalPassword;
                $utilizador->save();
                $newProfessorAdded += 1;
            }

            //estamos presentes num teste
            if ($aula[$i]['componente'] == null) {
                $testes += 1;
                continue;
            }

            $turnoNr = $aula[$i]['turno'] == "Sem Turno" ? 0 : $aula[$i]['turno'];

            $turno = Turno::where('tipo', $aula[$i]['componente'])->where('numero', $turnoNr)->where('idAnoletivo', $idAnoLetivo)->where('idCadeira', $cadeira->id)->first();

            //o turno n existe, sair ou um erro ou n inserir? pensar
            if (empty($turno)) {
                $turnonotfound += 1;
                continue;
            }

            //motivo de falta ou algo do genero, comfirmar se e suposto ficar assim!
/*            if($aula[$i]['motivo_falta != null && $aula[$i]['motivo_falta != '&nbsp;'){
                continue;
            }*/

            $newaula = Aula::where('idAntigo', $aula[$i]['id_aulas'])->first();
            if (empty($newaula)) {
                $newaula = Aula::where('data', date('Y-m-d', strtotime($aula[$i]['data'])))->where('horaInicio', date('H:i', strtotime($aula[$i]['data_inicio'])))
                    ->where('horaFim', date('H:i', strtotime($aula[$i]['data_fim'])))->where('idTurno', $turno->id)->where('idProfessor', $utilizador->id)->first();
                if (!empty($newaula)) {
                    continue;
                }
            }
            if (empty($newaula)) {
                //nova aula
                $newAula += 1;
                $newaula = new Aula();
                $newaula->idAntigo = $aula[$i]['id_aulas'];
            }
            $newaula->data = date('Y-m-d', strtotime($aula[$i]['data']));
            $newaula->horaInicio = date('H:i', strtotime($aula[$i]['data_inicio']));
            $newaula->horaFim = date('H:i', strtotime($aula[$i]['data_fim']));
            $newaula->idTurno = $turno->id;
            $newaula->idProfessor = $utilizador->id;
            $newaula->save();
            $dataChanged += 1;
        }
        return [
            'newProfessorAdded' => $newProfessorAdded,
            'turnonotfound' => $turnonotfound,
            'cadeiranotfound' => $cadeiranotfound,
            'dataChanged' => $dataChanged,
            'newAula' => $newAula,
            'testes' => $testes,
            'json' => $request
        ];
    }

    public function inscreverAlunosTurnosUnicos(Anoletivo $anoletivo, $semestre)
    {
        $turnos = Turno::select('turno.*')->rightjoin('cadeira', function ($join) use (&$semestre) {
            $join->on('turno.idCadeira', '=', 'cadeira.id')->where('cadeira.semestre', '=', $semestre);
        })->where('turno.idAnoletivo', '=', $anoletivo->id)->where('turno.numero', '=', 0)->get();
        $newInsc = 0;
        $failedIns = 0;
        foreach ($turnos as $turno) {
            $ids = Inscricao::where('idTurno', $turno->id)->pluck('idUtilizador')->toArray();

            $inscIds = Inscricaoucs::where('inscricaoucs.idCadeira', '=', $turno->idCadeira)
                ->where('inscricaoucs.estado', '=', 1)->where('inscricaoucs.idAnoletivo', '=', $anoletivo->id)
                ->whereNotIn('inscricaoucs.idUtilizador', $ids)->pluck('idUtilizador')->toArray();
            $data = [];
            foreach ($inscIds as $inscId) {
                array_push($data, ['idUtilizador' => $inscId, 'idTurno' => $turno->id]);

            }
            if (!empty($data)) {
                $newInsc_new = Inscricao::insertOrIgnore($data);
                $newInsc += $newInsc_new;
                Turno::where('id', $turno->id)->update(['vagasocupadas' => DB::raw('vagasocupadas+' . $newInsc_new)]);
                $failedIns = count($data) - $newInsc;
            }
        }
        return [
            'novasInscricoes' => $newInsc,
            'inscricoesFalharam' => $failedIns
        ];
    }
}