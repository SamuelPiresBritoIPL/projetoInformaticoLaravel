<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nome
 * @property int $idCadeira
 * @property int $vagastotal
 * @property int $vagasocupadas
 * @property integer $visivel
 * @property integer $repetentes
 * @property Cadeira $cadeira
 * @property Aula[] $aulas
 * @property Inscricao[] $inscricaos
 */
class Turno extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'turno';

    /**
     * @var array
     */
    protected $fillable = ['idCadeira', 'vagastotal', 'vagasocupadas', 'visivel', 'repetentes', 'tipo','numero','idanoletivo', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cadeira()
    {
        return $this->belongsTo(Cadeira::class, 'idCadeira');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aulas()
    {
        return $this->hasMany(Aula::class, 'idTurno');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inscricaos()
    {
        return $this->hasMany(Inscricao::class, 'idTurno');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inscricaosutilizadores()
    {
        return $this->hasMany(Inscricao::class, 'idTurno')
            ->join('utilizador','utilizador.id','=','inscricao.idUtilizador')
            ->leftjoin('inscricaoucs', function($join)
            {
                $join->on('inscricaoucs.idUtilizador', '=', 'utilizador.id');
                $join->where('inscricaoucs.idCadeira','=',$this->idCadeira);
                $join->where('inscricaoucs.estado','=',1);
            })
            ->select('inscricao.id','utilizador.login','utilizador.nome', 'utilizador.id AS idutilizador','inscricaoucs.nrinscricoes');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function anoletivo()
    {
        return $this->belongsTo(Anoletivo::class, 'idAnoletivo');
    }
}
