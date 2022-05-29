<?php

namespace App\Services;

use App\Models\Turno;
use App\Models\Cadeira;
use App\Models\Anoletivo;
use App\Models\Inscricao;
use App\Models\Utilizador;
use App\Models\Inscricaoucs;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

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

        $anoletivo = Anoletivo::where("id", $turno->idAnoletivo)->first();
        if(empty($anoletivo)){
            return ['msg' => "Ano letivo não definido",'code' => 404];
        }

        $inscricaouc = Inscricaoucs::where('idUtilizador',$utilizador->id)->where('idCadeira',$turno->cadeira->id)->where('idAnoletivo',$anoletivo->id)->where('estado',1)->first();
        if(empty($inscricaouc)){
            return ['msg' => "O aluno não está inscrito nesta unidade curricular",'code' => 404];
        }

        $inscricao = Inscricao::where('idUtilizador',$utilizador->id)->where('idTurno',$turno->id)->first();
        
        if(!empty($inscricao)){
            return ['msg' => "O aluno já está inscrito neste turno",'code' => 404];
        }
        $inscricao = new Inscricao();
        $inscricao->idTurno = $turno->id;
        $inscricao->idUtilizador = $utilizador->id;
        $inscricao->save();
        Turno::where('id', $turno->id)->update(['vagasocupadas' => DB::raw('vagasocupadas+1')]);
        return ['msg' => "Aluno adicionado ao turno com sucesso",'code' => 200];
    }

    public function ExportExcel($data_array,Cadeira $cadeira){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($data_array);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $cadeira->nome . '.xls"');
            header('Cache-Control: max-age=0');
            header('name: max-age=0');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With, Application');
            header('Access-Control-Expose-Headers: Content-Disposition');
    
            ob_end_clean();
            //$Excel_writer->save('php://output');
            $Excel_writer->save('php://output');
            return ['msg' => "hmmmm",'code' => 200];
            //exit();
        } catch (Exception $e) {
            return ['msg' => "Algo aconteceu",'code' => 200];
        }
    }

    /**
    *This function loads the customer data from the database then converts it
    * into an Array that will be exported to Excel
    */
   function exportCadeira(Cadeira $cadeira){
        $data_array [] = array("Unidade curricular:",$cadeira->codigo,$cadeira->nome);
        $data_array [] = array("Alunos:");
        $data_array [] = array("Numero","Nome");
        $alunos = Utilizador::join('inscricaoucs','utilizador.id', '=', 'inscricaoucs.idUtilizador')
                            ->where('inscricaoucs.estado',1)->where('inscricaoucs.idCadeira',$cadeira->id)->select('utilizador.*')->get();
        foreach($alunos as $utilizadores)
        {
            $data_array[] = array(
                'Numero' =>$utilizadores->login,
                'Nome' => $utilizadores->nome,
            );
        }
        return $this->ExportExcel($data_array,$cadeira);
    }

    public function mudarVisibilidade(Cadeira $cadeira,Anoletivo $anoletivo,$visivel){
        $turnos = Cadeira::where('cadeira.id',$cadeira->id)->join('turno','turno.idCadeira','=','cadeira.id')
                        ->where('turno.idAnoletivo', $anoletivo->id)->update(['turno.visivel'=>$visivel]);
        return ['msg' => "Turnos atualizados",'code' => 200];
    }
}