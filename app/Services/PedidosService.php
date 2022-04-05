<?php

namespace App\Services;

use App\Models\Logs;
use App\Models\Pedidos;
use App\Models\Pedidosucs;

class PedidosService
{
    public function save($data){
        $pedido = new Pedidos();
        $pedido->idUtilizador = $data->get('idUtilizador');
        $pedido->idAnoLetivo = $data->get('idAnoletivo');
        $pedido->descricao = $data->get('descricao');
        $pedido->estado = $data->get('estado');
        $pedido->semestre = $data->get('semestre');

        $pedido->save();
        return $pedido;
    }

    public function update($pedido, $data){
        $pedido->descricao = $data->get('descricao');
        $pedido->estado = $data->get('estado');

        $pedido->save();
        return $pedido;
    }


    public function savePedidosUcs($pedidosId, $cadeiraId){
        $pedidoucs = new Pedidosucs();
        $pedidoucs->idCadeira = $cadeiraId;
        $pedidoucs->idPedidos = $pedidosId;

        $pedidoucs->save();
        return $pedidoucs;
    }


    public function checkifExists($data){
        if($data->get('estado') == 0){
            $pedidos = Pedidos::where('idUtilizador', $data->get('idUtilizador'))->where('idAnoletivo',$data->get('idAnoletivo'))->where('semestre',$data->get('semestre'))->where('estado',0)->first();
            if(!empty($pedidos)){
                return ['response' => 0, 'erro' => 'Já existe uma confirmação'];
            }
            return ['response' => 1];
        }
        

        $pedidos = Pedidos::where('idUtilizador', $data->get('idUtilizador'))->where('idAnoletivo',$data->get('idAnoletivo'))->where('semestre',$data->get('semestre'))->where('estado',1)->get();
        if(!empty($pedidos)){
            foreach($pedidos as $pedido){
                foreach($pedido->pedidosucs as $pedidoucs){
                    if(in_array($pedidoucs->cadeira->id, $data->get('cadeirasIds'))){
                        return ['response' => 0, 'erro' => 'Já têm um pedido pendente para a cadeira: ' . $pedidoucs->cadeira->nome . ' '];
                    }
                }
            }
        }
        return ['response' => 1];
    }
}