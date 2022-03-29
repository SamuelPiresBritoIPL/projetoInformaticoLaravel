<?php

namespace App\Http\Resources;

use App\Http\Resources\UtilizadorResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CoordenadorResource extends JsonResource
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
            'tipo' => $this->tipo,
            'utilizador' => new UtilizadorResource($this->utilizador)
        ];
    }
}
