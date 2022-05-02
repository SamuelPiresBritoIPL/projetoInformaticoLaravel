<?php

namespace App\Http\Resources;

use App\Services\CursoService;
use App\Http\Resources\CoordenadorResource;
use App\Http\Resources\CursoResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CadeiraResourceCollection;

class CursoResource extends JsonResource
{
    protected $anoletivo;
    protected $semestre;

    public function anoletivo($value, $value2){
      $this->anoletivo = $value;
      $this->semestre = $value2;
      return $this;
    }
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
            'cadeiras' => CadeiraResourceCollection::make($this->cadeiras->where('semestre', $this->semestre))->anoletivo($this->anoletivo, $this->semestre)
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
    public static function collection($resource){
      return new CursoResourceCollection($resource);
    }
}
