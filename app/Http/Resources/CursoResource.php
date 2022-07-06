<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\Aula;
use App\Services\CursoService;
use Illuminate\Support\Facades\Auth;
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
          $aula = Aula::join('turno', 'turno.id','=','aula.idTurno')->join('cadeira', 'cadeira.id','=','turno.idCadeira')
          ->join('curso', 'curso.id','=','cadeira.idCurso')->where('curso.id',$this->id)->orderby('aula.updated_at', 'DESC')->select('aula.*')->first();
          CadeiraResource::$format = 'paracurso';
          return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'abreviatura' => $this->abreviatura,
            'ultimoupdateaula' => (isset($aula->updated_at) ? "(".date('d-m-Y',strtotime($aula->updated_at)).")" : "(Sem horário)"),
            'cadeiras' => CadeiraResourceCollection::make($this->cadeiras->where('semestre', $this->semestre))->anoletivo($this->anoletivo, $this->semestre)
          ];
        case 'coordenador':
          return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'abreviatura' => $this->abreviatura,
            'coordenadores' => CoordenadorResource::collection($this->coordenadors->where('idUtilizador','!=',Auth::user()->id))
          ];
        case 'aberturas':
          //nr de anos de um curso enviar aqui
          $anosCurso = (new CursoService)->getAnosCurso($this->id);
          $pedidosCurso = (new CursoService)->getPedidosCurso($this->id);
          return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'abreviatura' => $this->abreviatura,
            'totalanos' => $anosCurso,
            'totalpedidos' => $pedidosCurso,
            'aberturas' => AberturaResource::collection($this->aberturas->where('semestre', $this->semestre)->where('idAnoletivo', $this->anoletivo))
          ];
        case 'aberturasDashboard':
          //nr de anos de um curso enviar aqui
          $anosCurso = (new CursoService)->getAnosCurso($this->id);
          $pedidosCurso = (new CursoService)->getPedidosCurso($this->id);
          return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'abreviatura' => $this->abreviatura,
            'totalanos' => $anosCurso,
            'totalpedidos' => $pedidosCurso,
            'aberturas' => AberturaResource::collection($this->aberturasdefinidas->where('semestre', $this->semestre)->where('idAnoletivo', $this->anoletivo))
          ];
        default:
        $aula = Aula::join('turno', 'turno.id','=','aula.idTurno')->join('cadeira', 'cadeira.id','=','turno.idCadeira')
        ->join('curso', 'curso.id','=','cadeira.idCurso')->where('curso.id',$this->id)->orderby('aula.updated_at', 'DESC')->select('aula.*')->first();
        return [
          'id' => $this->id,
          'codigo' => $this->codigo,
          'nome' => $this->nome,
          'abreviatura' => $this->abreviatura,
          'ultimoupdateaula' => isset($aula->updated_at) ? "(".date('d-m-Y',strtotime($aula->updated_at)).")" : "(Sem horário)"
        ];
      }  
    }
    public static function collection($resource){
      return new CursoResourceCollection($resource);
    }
}
