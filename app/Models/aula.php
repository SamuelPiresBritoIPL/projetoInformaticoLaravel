<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property integer $diaSemana
 * @property string $horaInicio
 * @property string $horaFim
 * @property int $idTurno
 * @property Turno $turno
 */
class aula extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'aula';

    /**
     * @var array
     */
    protected $fillable = ['diaSemana', 'horaInicio', 'horaFim', 'idTurno'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function turno()
    {
        return $this->belongsTo(turno::class, 'idTurno');
    }
}
