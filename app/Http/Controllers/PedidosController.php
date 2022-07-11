<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Cadeira;
use App\Models\Pedidos;
use App\Models\Aberturas;
use App\Models\Anoletivo;
use App\Models\Inscricao;
use App\Models\Pedidosucs;
use App\Models\Inscricaoucs;
use Illuminate\Http\Request;
use App\Services\PedidosService;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PedidosResource;
use App\Http\Requests\PedidosPostRequest;

class PedidosController extends Controller
{
    public function store(PedidosPostRequest $request){
        //criar pedidos
        $data = collect($request->validated());
        $canBeCreated = (new PedidosService)->checkifExists($data);
        if($canBeCreated['response'] == 0){
            return response($canBeCreated['erro'],401);
        }
        $anoletivo = Anoletivo::where('ativo',1)->first();
        $cadeirasNotIn = Inscricaoucs::where('idUtilizador', Auth::user()->id)->where('estado',1)
                        ->whereIn('idCadeira',$data["cadeirasIds"])->where('idAnoletivo',$anoletivo->id)->pluck('idCadeira')->toArray();

        (new PedidosService)->removerUcs($cadeirasNotIn, Auth::user()->id, $anoletivo->id);
        
        $cadeirasNotIn = array_diff($data["cadeirasIds"],$cadeirasNotIn);
        $cadeiras = Cadeira::whereIn('cadeira.id', $cadeirasNotIn)->join('curso', 'cadeira.idCurso', '=', 'curso.id')
                    ->select('cadeira.id', 'cadeira.ano', 'cadeira.semestre', 'curso.nome', 'curso.id as idCurso')->get();
                    
        $cursos = [];
        foreach ($cadeiras as $key => $cadeira) {
            if(!array_key_exists($cadeira->idCurso,$cursos)){
                $cursos[$cadeira->idCurso] = [];
            }
            array_push($cursos[$cadeira->idCurso], $cadeira);
        }
        
        $pedidos = [];
        foreach ($cursos as $key => $curso) {
            
            $pedido = Pedidos::where('idUtilizador', $data->get('idUtilizador'))->where('idAnoletivo',$data->get('idAnoletivo'))
                        ->where('semestre',$data->get('semestre'))->where('estado',0)->where('idCurso', $key)->first();
            
            if(!is_null($pedido)){
                $pedido = (new PedidosService)->update($pedido, $data);
            }else{
                $pedido = (new PedidosService)->save($data, $key);
            }
            
            if($pedido->estado == 1){
                foreach($curso as $cadeira){
                    //ciclo para perceber se é para adicionar ucs ou remover ucs
                    (new PedidosService)->savePedidosUcs($pedido->id,$cadeira->id);
                }
            }
            array_push($pedidos, $pedido);
        }
        return response((PedidosResource::collection($pedidos)),201);
    }

    public function getPedidosByCurso(Curso $curso, Anoletivo $anoletivo,$semestre){
        $pedidos = Pedidos::where('idAnoletivo', $anoletivo->id)->where('semestre',$semestre)->where('estado', 1)->where('idCurso', $curso->id)->get();
        $pedidosAntigos = Pedidos::where('idAnoletivo', $anoletivo->id)->where('semestre',$semestre)->whereIn('estado', [2,3,4])->where('idCurso', $curso->id)->orderby('pedidos.updated_at','DESC')->get();
        return response(["pedidos"=>PedidosResource::collection($pedidos), "pedidosntigos"=>PedidosResource::collection($pedidosAntigos)],200);
    }

    public function editPedidoByCoordenador(PedidosPostRequest $request, Pedidos $pedido){

        $data = collect($request->validated());

        $result = (new PedidosService)->editPedidoByAdmin($data, $pedido);

        return response($result["msg"],$result["code"]);

    }
}
