<?php

namespace App\Http\Resources;

use App\Http\Resources\UtilizadorResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LogsResource extends JsonResource
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
            'descricao' => $this->descricao,
            'tabela' => $this->tabela,
            'utilizador' => $this->utilizador,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
