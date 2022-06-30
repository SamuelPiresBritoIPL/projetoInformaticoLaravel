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
class Aula extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'aula';

    
    /*protected $casts = [
        'data' => 'date',
        'horaInicio' => 'time',
        'horaFim' => 'time'
    ];*/

    /**
     * @var array
     */
    protected $fillable = ['idAntigo','data', 'horaInicio', 'horaFim', 'idTurno', 'idProfessor',];

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
    public function professor()
    {
        return $this->belongsTo(Utilizador::class, 'idProfessor');
    }
}
