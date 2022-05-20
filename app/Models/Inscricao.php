<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $idUtilizador
 * @property int $idTurno
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Turno $turno
 * @property Utilizador $utilizador
 */
class Inscricao extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'inscricao';

    /**
     * @var array
     */
    protected $fillable = ['idUtilizador', 'idTurno', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function turno()
    {
        return $this->belongsTo(Turno::class, 'idTurno');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function utilizador()
    {
        return $this->belongsTo(Utilizador::class, 'idUtilizador');
    }
}
