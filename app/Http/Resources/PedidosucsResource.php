<?php

namespace App\Http\Resources;

use App\Http\Resources\UtilizadorResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PedidosucsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        CadeiraResource::$format = 'inscricaoucs';
        return [
            'id' => $this->id,
            'aceite' => $this->aceite,
            'cadeira' => new CadeiraResource($this->cadeira)
        ];
    }
}
