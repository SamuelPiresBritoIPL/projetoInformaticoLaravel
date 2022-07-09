<?php

namespace App\Http\Resources;

use App\Models\Turno;
use App\Models\Anoletivo;
use App\Models\Inscricao;
use App\Models\Inscricaoucs;
use Database\Seeders\TurnoSeeder;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CursoResource;
use App\Http\Resources\TurnoResource;
use App\Http\Resources\CoordenadorResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CadeiraResource extends JsonResource
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
      switch (CadeiraResource::$format) {
        case 'inscricaoucsuser':
          TurnoResource::$format = 'paracadeiraturno';
          return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'ano' => $this->ano,
            'semestre' => $this->semestre,
            'abreviatura' => $this->abreviatura,
            'curso' => $this->curso->nome,
            'codigocurso' => $this->curso->codigo,
            'estado' => $this->estado
          ];
        case 'inscricaoucs':
          TurnoResource::$format = 'paracadeiraturno';
          $anoletivo = Anoletivo::where('ativo',1)->first();
          $turnos = Turno::join('inscricaoucs', function ($join) use(&$request, &$anoletivo) {
            $join->on('turno.idCadeira', '=', 'inscricaoucs.idCadeira')
            ->where('inscricaoucs.idUtilizador', '=', $request->user()->id)
            ->where('inscricaoucs.estado', 1)
            ->where('turno.numero', '>' , 0)
            ->where('turno.visivel', '=', 1)
            ->where('turno.idAnoletivo', $anoletivo->id);
          })->join('cadeira', function ($join) {
            $join->on('turno.idCadeira', '=', 'cadeira.id')
            ->where('cadeira.idCurso', '=', $this->curso->id)
            ->where('cadeira.id', '=', $this->id);
          })->orderBy('tipo', 'DESC')->orderBy('numero', 'ASC')->select('turno.*')->get();
          $inscricaoucs = Inscricaoucs::where('idUtilizador',$request->user()->id)->where('idAnoletivo',$anoletivo->id)->where('nrinscricoes','>',1)->where('idCadeira',$this->id)->first();
          if(isset($inscricaoucs)){
            for ($i = 0; $i < count($turnos) ; $i++) {
              if($turnos[$i]->repetentes == 0){
                //dd($turnos);
                $turnos->forget($i);
              }
            }
          }
          return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'ano' => $this->ano,
            'semestre' => $this->semestre,
            'abreviatura' => $this->abreviatura,
            'curso' => $this->curso->nome,
            'codigocurso' => $this->curso->codigo,
            'estado' => $this->estado,
            'turnos' => (TurnoResource::collection($turnos))->groupBy('tipo')
          ];
        case 'paracurso':
          //ir buscar numero total inscritos em quantos
          $totalInscricoes = Inscricaoucs::where('idCadeira', $this->id)->where('estado', 1)->where('idAnoletivo', $this->anoletivo)->count();
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
            'turnos' => (TurnoResource::collection($this->turnos->where('idAnoletivo', $this->anoletivo)))->groupBy('tipo'),
          ];
        case 'paraprofessor':
          TurnoResource::$format = 'paracadeira';
          return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'ano' => $this->ano,
            'semestre' => $this->semestre,
            'nome' => $this->nome,
            'abreviatura' => $this->abreviatura,
            'turnos' => (TurnoResource::collection($this->turnos->where('idAnoletivo', $this->anoletivo))),
            'curso' => $this->curso,
        ];
        default:
            return [
                'id' => $this->id,
                'codigo' => $this->codigo,
                'ano' => $this->ano,
                'semestre' => $this->semestre,
                'nome' => $this->nome,
                'abreviatura' => $this->abreviatura,
                'turnos' => TurnoResource::collection($this->turnos->where('idAnoletivo', $this->anoletivo)),
                'curso' => $this->curso,
            ];
        }  
    }
    public static function collection($resource){
      return new CadeiraResourceCollection($resource);
    }
}
