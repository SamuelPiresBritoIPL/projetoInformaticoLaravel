<?php

namespace App\Http\Resources;

use App\Http\Resources\UtilizadorResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TurnoResource extends JsonResource
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
      switch (TurnoResource::$format) {
        case 'paracadeira':
            $vagasocupadas = 0;
            if(!is_null($this->vagastotal)){
                //ir buscar mais dados para as vagasocupadas
            }
          return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'numero' => $this->numero,
            'vagastotal' => $this->vagastotal,
            'vagasocupadas' => $vagasocupadas,
          ];
        case 'paracadeiraturno':
            $vagasocupadas = 0;
            if(!is_null($this->vagastotal)){
                //ir buscar mais dados para as vagasocupadas
            }
          return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'numero' => $this->numero
          ];
        default:
        return [
          'id' => $this->id,
          'vagastotal' => $this->vagastotal,
          'visivel' => $this->visivel,
          'repetentes' => $this->repetentes,
          'tipo' => $this->tipo,
          'numero' => $this->numero,
          'cadeira' => $this->cadeira,
        ];
      }  
    }
}
