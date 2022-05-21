<?php

namespace App\Services;

use App\Models\Turno;
use App\Models\Anoletivo;
use App\Models\Inscricaoucs;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TurnoResource;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

class TurnoService
{
    public function getInformacoesTurnoForAdmin($turno){
        $anoletivo = Anoletivo::where("ativo", 1)->first();
        if(empty($anoletivo)){
            return ['msg' => "Error",'code' => 404];
        }
        $alunos = $turno->inscricaosutilizadores;
        $totalNaorepetentes = 0;
        $totalRepetentes = 0;
        foreach ($alunos as $aluno) {
            if($aluno->nrinscricoes == 1){
                $totalNaorepetentes++;
            }else{
                $totalRepetentes++;
            }
        }
        $send = ["totalinscritos" => $totalNaorepetentes+$totalRepetentes,"totalrepetentes" => $totalRepetentes,
                "totalnaorepetentes" => $totalNaorepetentes,"turno"=> new TurnoResource($turno), "alunos" => $alunos];
        return ['msg' => $send,'code' => 200];
    }

    public function editTurno($data,$turno){
        if($data->has('visivel')){
            $turno->visivel = $data->get('visivel');
        }
        if($data->has('repetentes')){
            $turno->repetentes = $data->get('repetentes');
        }
        if($data->has('vagastotal')){
            $turno->vagastotal = $data->get('vagastotal');
        }
        $turno->save();
        return ['msg' => "Alterações feitas com sucesso",'code' => 200];
    }

    public function ExportExcel($data_array,Turno $turno){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($data_array);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $turno->tipo . ($turno->numero) . '_' . $turno->cadeira->nome . '.xls"');
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
   function exportTurno(Turno $turno){
        $data_array [] = array("Unidade curricular:",$turno->cadeira->codigo,$turno->cadeira->nome);
        $data_array [] = array("Turno:",$turno->tipo . ($turno->numero != 0 ? $turno->numero : ""));
        $data_array [] = array("Alunos:");
        $data_array [] = array("Numero","Nome");
        foreach($turno->inscricaosutilizadores as $utilizadores)
        {
            $data_array[] = array(
                'Numero' =>$utilizadores->login,
                'Nome' => $utilizadores->nome,
            );
        }
        return $this->ExportExcel($data_array,$turno);
    }
}