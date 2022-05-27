<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UtilizadorResource extends JsonResource
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
            'login' => $this->login,
            'nome' => $this->nome,
            'idCurso' => !empty($this->curso) ? $this->curso->id : "",
            'curso' => !empty($this->curso) ? $this->curso->nome : "",
            'codigoCurso' => !empty($this->curso) ? $this->curso->codigo : ""
        ];
    }
}
