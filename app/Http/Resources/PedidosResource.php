<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PedidosResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
      return [
        'id' => $this->id,
        'estado' => $this->estado,
        'descricao' => $this->descricao,
        'utilizador' => new UtilizadorResource($this->utilizador),
        'anoletivo' => $this->anoletivo,
        'cadeiras' => PedidosucsResource::collection($this->pedidosucs)
      ];
    }
}
