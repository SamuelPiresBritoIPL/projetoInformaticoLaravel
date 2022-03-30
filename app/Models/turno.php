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
    protected $fillable = ['nome', 'idCadeira', 'vagastotal', 'visivel', 'idProfessor', 'tipo','numero'];

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function professor()
    {
        return $this->belongsTo(utilizador::class, 'idProfessor');
    }
}
