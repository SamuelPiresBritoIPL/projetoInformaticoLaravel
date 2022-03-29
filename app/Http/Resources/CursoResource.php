<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CoordenadorResource;

class CursoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public static $format = 'default';
    public function toArray($request)
    {
      switch (CursoResource::$format) {
        case 'cadeira':
          return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'abreviatura' => $this->abreviatura,
            'cadeiras' => $this->cadeiras
          ];
        case 'coordenador':
          return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'abreviatura' => $this->abreviatura,
            'coordenadores' => CoordenadorResource::collection($this->coordenadors)
          ];
        default:
        return [
          'id' => $this->id,
          'codigo' => $this->codigo,
          'nome' => $this->nome,
          'abreviatura' => $this->abreviatura
        ];
      }  
    }
}
