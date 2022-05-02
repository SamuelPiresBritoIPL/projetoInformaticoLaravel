<?php

namespace App\Http\Resources;

use App\Http\Resources\CadeiraResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CadeiraResourceCollection extends ResourceCollection{

    protected $anoletivo;
    protected $semestre;

    public function anoletivo($value, $value2){
      $this->anoletivo = $value;
      $this->semestre = $value2;
      return $this;
    }

    public function toArray($request){
        return $this->collection->map(function(CadeiraResource $resource) use($request){
            return $resource->anoletivo($this->anoletivo, $this->semestre)->toArray($request);
        })->all();
    }
}