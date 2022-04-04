<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CoordenadorResource;
use App\Services\CursoService;

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
          CadeiraResource::$format = 'paracurso';
          return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'abreviatura' => $this->abreviatura,
            'cadeiras' => CadeiraResource::collection($this->cadeiras)
          ];
        case 'coordenador':
          return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'abreviatura' => $this->abreviatura,
            'coordenadores' => CoordenadorResource::collection($this->coordenadors)
          ];
        case 'aberturas':
          //nr de anos de um curso enviar aqui
          $anosCurso = (new CursoService)->getAnosCurso($this->id);
          return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'abreviatura' => $this->abreviatura,
            'totalanos' => $anosCurso,
            'aberturas' => AberturaResource::collection($this->aberturas)
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
