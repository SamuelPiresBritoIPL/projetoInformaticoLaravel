<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Pedidos;
use App\Models\Aberturas;
use App\Models\Anoletivo;
use App\Models\Inscricao;
use App\Models\Pedidosucs;
use App\Models\Inscricaoucs;
use Illuminate\Http\Request;
use App\Services\PedidosService;
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
        $pedido = Pedidos::where('idUtilizador', $data->get('idUtilizador'))->where('idAnoletivo',$data->get('idAnoletivo'))->where('semestre',$data->get('semestre'))->where('estado',0)->first();
        if(!empty($pedido)){
            $pedido = (new PedidosService)->update($pedido, $data);
        }else{
            $pedido = (new PedidosService)->save($data);
        }
        
        if($pedido->estado == 1){
            foreach($data->get('cadeirasIds') as $cadeiraId){
                (new PedidosService)->savePedidosUcs($pedido->id,$cadeiraId);
            }
        }
        return response((new PedidosResource($pedido)),201);
    }

    public function getPedidosByCurso(Curso $curso, Anoletivo $anoletivo,$semestre){
        $pedidos = Pedidos::where('idAnoletivo', $anoletivo->id)->where('semestre',$semestre)->rightjoin('utilizador', function ($join) use(&$curso) {
            $join->on('utilizador.id', '=', 'pedidos.idUtilizador')
                 ->where('utilizador.idCurso','=',$curso->id);
        })->select('pedidos.*')->get();
        return response(PedidosResource::collection($pedidos),200);
    }

    public function editPedidoByCoordenador(PedidosPostRequest $request, Pedidos $pedido){
        $data = collect($request->validated());

        $result = (new PedidosService)->editPedidoByAdmin($data, $pedido);

        return response($result["msg"],$result["code"]);

    }
}
