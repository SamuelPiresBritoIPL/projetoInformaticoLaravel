<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nome
 * @property int $idCadeira
 * @property int $vagastotal
 * @property integer $visivel
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
    protected $fillable = ['idCadeira', 'vagastotal', 'visivel', 'tipo','numero','idanoletivo', 'created_at', 'updated_at', 'deleted_at'];

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function anoletivo()
    {
        return $this->belongsTo(Anoletivo::class, 'idAnoletivo');
    }
}
