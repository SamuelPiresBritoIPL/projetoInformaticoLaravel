<?php

namespace App\Http\Resources;

use App\Http\Resources\CursoResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CursoResourceCollection extends ResourceCollection{

    protected $anoletivo;
    protected $semestre;

    public function anoletivo($value, $value2){
      $this->anoletivo = $value;
      $this->semestre = $value2;
      return $this;
    }

    public function toArray($request){
        return $this->collection->map(function(CursoResource $resource) use($request){
            return $resource->anoletivo($this->anoletivo, $this->semestre)->toArray($request);
        })->all();
    }
}