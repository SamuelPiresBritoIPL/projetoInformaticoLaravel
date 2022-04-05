<?php

namespace App\Http\Controllers;

use App\Http\Requests\PedidosPostRequest;
use App\Http\Resources\PedidosResource;
use App\Models\Anoletivo;
use App\Models\Pedidos;
use App\Services\PedidosService;

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
}
