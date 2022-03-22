<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $dataAbertura
 * @property string $dataEncerar
 * @property integer $ano
 * @property integer $tipoAbertura
 * @property int $idUtilizador
 * @property int $idCurso
 * @property Utilizador $utilizador
 * @property Curso $curso
 */
class aberturas extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['dataAbertura', 'dataEncerar', 'ano', 'tipoAbertura', 'idUtilizador', 'idCurso'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function utilizador()
    {
        return $this->belongsTo(Utilizador::class, 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'idCurso');
    }
}
