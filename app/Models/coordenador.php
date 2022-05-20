<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $idUtilizador
 * @property string $tipo 0 -> coordenador | 1 -> subcoordenador
 * @property int $idCurso
 * @property string $created_at
 * @property string $updated_at
 * @property Curso $curso
 * @property Utilizador $utilizador
 */
class Coordenador extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'coordenador';

    /**
     * @var array
     */
    protected $fillable = ['idUtilizador', 'tipo', 'idCurso', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'idCurso');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function utilizador()
    {
        return $this->belongsTo(Utilizador::class, 'idUtilizador');
    }
}
