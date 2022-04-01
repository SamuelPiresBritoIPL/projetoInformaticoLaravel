<?php

namespace App\Http\Resources;

use App\Http\Resources\UtilizadorResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AberturaResource extends JsonResource
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
            'dataAbertura' => $this->dataAbertura,
            'dataEncerar' => $this->dataEncerar,
            'ano' => $this->ano,
            'tipoAbertura' => $this->tipoAbertura,
            'anoletivo' => $this->anoletivo,
            'semestre' => $this->semestre,
            'utilizador' => new UtilizadorResource($this->utilizador)
        ];
    }
}
