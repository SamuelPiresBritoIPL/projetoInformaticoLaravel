<?php

namespace App\Http\Resources;

use App\Http\Resources\TurnoResource;
use App\Http\Resources\CoordenadorResource;
use Database\Seeders\TurnoSeeder;
use Illuminate\Http\Resources\Json\JsonResource;

class CadeiraResource extends JsonResource
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
      switch (CadeiraResource::$format) {
        case 'inscricaoucs':
          return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'abreviatura' => $this->abreviatura
          ];
        case 'paracurso':
          TurnoResource::$format = 'paracadeira';
          return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'ano' => $this->ano,
            'semestre' => $this->semestre,
            'nome' => $this->nome,
            'abreviatura' => $this->abreviatura,
            'turnos' => TurnoResource::collection($this->turnos),
          ];
        default:
        return [
          'id' => $this->id,
            'codigo' => $this->codigo,
            'ano' => $this->ano,
            'semestre' => $this->semestre,
            'nome' => $this->nome,
            'abreviatura' => $this->abreviatura,
            'turnos' => TurnoResource::collection($this->turnos),
            'curso' => CursoResource::collection($this->curso),
        ];
      }  
    }
}
