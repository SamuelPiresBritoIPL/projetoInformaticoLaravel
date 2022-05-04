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
            'cadeira' => new CadeiraResource($this->cadeira),
            'estado' => $this->estado
          ];
        default:
        return [
          'id' => $this->id,
        ];
      }  
    }
}
