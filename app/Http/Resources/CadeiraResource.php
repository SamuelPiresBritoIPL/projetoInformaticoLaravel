<?php

namespace App\Http\Resources;

use App\Http\Resources\TurnoResource;
use App\Http\Resources\CoordenadorResource;
use App\Models\Inscricao;
use App\Models\Inscricaoucs;
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
            'ano' => $this->ano,
            'semestre' => $this->semestre,
            'abreviatura' => $this->abreviatura,
            'estado' => $this->estado
          ];
        case 'paracurso':
          //ir buscar numero total inscritos em quantos
          $totalInscricoes = Inscricaoucs::where('idCadeira', $this->id)->where('estado', 1)->where('idAnoletivo', 1)->count();
          //$totalInscritos = Inscricao::where('')
          TurnoResource::$format = 'paracadeira';
          return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'ano' => $this->ano,
            'semestre' => $this->semestre,
            'nome' => $this->nome,
            'abreviatura' => $this->abreviatura,
            'nrInscricoes' => $totalInscricoes,
            'nrInscritos' => 0,
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
