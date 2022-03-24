<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CursoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public static $format = "default";
    public function toArray($request)
    {
        switch (CursoResource::$format) {
			case 'detailed':
				return [
					'codigo' => $this->codigo,
					'nome' => $this->nome,
					'abreviatura' => $this->abreviatura
				];
			default:
				return [
					'codigo' => $this->codigo,
					'nome' => $this->nome,
					'abreviatura' => $this->abreviatura,
					'planoscurriculares' => $this->planocurriculars,
				];
		}
        return parent::toArray($request);
    }
}
