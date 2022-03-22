<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nome
 * @property int $idCadeira
 * @property Cadeira $cadeira
 * @property Aula[] $aulas
 * @property Inscricao[] $inscricaos
 */
class turno extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'turno';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['nome', 'idCadeira'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cadeira()
    {
        return $this->belongsTo(cadeira::class, 'idCadeira');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aulas()
    {
        return $this->hasMany(aula::class, 'idTurno');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inscricaos()
    {
        return $this->hasMany(inscricao::class, 'idTurno');
    }
}
