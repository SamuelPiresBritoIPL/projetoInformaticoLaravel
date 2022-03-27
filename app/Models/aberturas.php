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
 * @property string $created_at
 * @property string $updated_at
 * @property Utilizador $utilizador
 * @property Curso $curso
 */
class aberturas extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['dataAbertura', 'dataEncerar', 'ano', 'tipoAbertura', 'idUtilizador', 'idCurso', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function utilizador()
    {
        return $this->belongsTo(utilizador::class, 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'idCurso');
    }
}
