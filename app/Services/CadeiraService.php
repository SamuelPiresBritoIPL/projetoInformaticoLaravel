<?php

namespace App\Services;

use App\Models\Cadeira;
use App\Models\Anoletivo;
use App\Models\Inscricao;
use App\Models\Utilizador;
use App\Models\Inscricaoucs;
use Illuminate\Support\Facades\DB;

class CadeiraService
{
    public function getInformacoesCadeirasForAdmin($cadeira, Anoletivo $anoletivo){
        $subQuery = "(select MAX(i.idTurno) from inscricao i join turno t on t.id = i.idTurno where i.idTurno IN 
        (SELECT t.id from turno t WHERE t.idCadeira = ". $cadeira->id ." and t.idAnoletivo = ". $anoletivo->id .") and i.idUtilizador = utilizador.id) as idTurno";
        $alunos = Inscricaoucs::where('idCadeira',$cadeira->id)->where('idAnoletivo', $anoletivo->id)->where('estado',1)
            ->join('utilizador', 'utilizador.id', '=', 'inscricaoucs.idUtilizador')
            ->select('utilizador.id','utilizador.nome','utilizador.login','inscricaoucs.nrinscricoes',DB::raw($subQuery))->get();
        $totalNaorepetentes = 0;
        $totalRepetentes = 0;
        $totalinscritosTurnos = 0;
        foreach ($alunos as $aluno) {
            if(!is_null($aluno->idTurno)){
                $totalinscritosTurnos++;
            }
            if($aluno->nrinscricoes == 1){
                $totalNaorepetentes++;
            }else{
                $totalRepetentes++;
            }
        }
        $send = ["totalinscritos" => $totalNaorepetentes+$totalRepetentes,"totalrepetentes" => $totalRepetentes,
                "totalnaorepetentes" => $totalNaorepetentes, "totalnaoinscritos" => ($totalNaorepetentes+$totalRepetentes)-$totalinscritosTurnos, "alunos" => $alunos];

        return ['msg' => $send,'code' => 200];
    }

    public function addStudentToUC($data,$cadeira){
        if($data->has('login')){
            if(str_contains($data->get('login'), '@')){
                $utilizador = Utilizador::where('email',$data->get('login'))->first();
            }else{
                $utilizador = Utilizador::where('login',$data->get('login'))->first();
            }
        }else{
            $utilizador = Utilizador::where('email',$data->get('email'))->first();
        }
        if(empty($utilizador)){
            return ['msg' => "Este utilizador não é válido",'code' => 404];
        }

        $anoletivo = Anoletivo::where("ativo", 1)->first();
        if(empty($anoletivo)){
            return ['msg' => "Ano letivo não definido",'code' => 404];
        }

        $inscricaouc = Inscricaoucs::where('idUtilizador',$utilizador->id)->where('idCadeira',$cadeira->id)->where('idAnoletivo',$anoletivo->id)->where('estado',1)->first();
        if(!empty($inscricaouc)){
            return ['msg' => "O aluno já está inscrito na unidade curricular",'code' => 404];
        }
        
        $inscricaouc = new Inscricaoucs();
        $inscricaouc->idCadeira = $cadeira->id;
        $inscricaouc->idUtilizador = $utilizador->id;
        $inscricaouc->idAnoletivo = $anoletivo->id;
        $inscricaouc->nrinscricoes = 1;
        $inscricaouc->save();
        return ['msg' => "Aluno adicionado com sucesso",'code' => 200];
    }

    public function addStudentToTurno($data,$turno){
        if($data->has('login')){
            if(str_contains($data->get('login'), '@')){
                $utilizador = Utilizador::where('email',$data->get('login'))->first();
            }else{
                $utilizador = Utilizador::where('login',$data->get('login'))->first();
            }
        }else{
            $utilizador = Utilizador::where('email',$data->get('email'))->first();
        }
        if(empty($utilizador)){
            return ['msg' => "Este utilizador não é válido",'code' => 404];
        }

        $anoletivo = Anoletivo::where("ativo", 1)->first();
        if(empty($anoletivo)){
            return ['msg' => "Ano letivo não definido",'code' => 404];
        }

        $inscricaouc = Inscricaoucs::where('idUtilizador',$utilizador->id)->where('idCadeira',$turno->cadeira->id)->where('idAnoletivo',$anoletivo->id)->where('estado',1)->first();
        if(empty($inscricaouc)){
            return ['msg' => "O aluno não está inscrito nersta unidade curricular",'code' => 404];
        }

        $inscricao = Inscricao::where('idUtilizador',$utilizador->id)->where('idTurno',$turno->id)->where('idAnoletivo',$anoletivo->id)->first();
        
        if(!empty($inscricao)){
            return ['msg' => "O aluno já está inscrito neste turno",'code' => 404];
        }
        $inscricao = new Inscricao();
        $inscricao->idTurno = $turno->id;
        $inscricao->idUtilizador = $utilizador->id;
        $inscricao->idAnoletivo = $anoletivo->id;
        $inscricao->save();
        return ['msg' => "Aluno adicionado ao turno com sucesso",'code' => 200];
    }
}