<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InscricaoucsResource extends JsonResource
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
      switch (InscricaoucsResource::$format) {
        case 'cadeiras':
          CadeiraResource::$format = 'inscricaoucs';
          return [
            'id' => $this->id,
            'idCadeira' => $this->idCadeira,
            'idUtilizador' => $this->idUtilizador,
            'idAnoletivo' => $this->idAnoletivo,
            'nrinscricoes' => $this->nrinscricoes,
            'cadeira' => new CadeiraResource($this->cadeira),
            'estado' => $this->estado,
            'idCurso' => $this->cadeira->curso->id,
            'nomeCurso' => $this->cadeira->curso->nome,
            'nomeCadeira' => $this->cadeira->nome
          ];
        default:
        return [
          'id' => $this->id,
        ];
      }  
    }
}
