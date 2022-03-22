<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $codigo
 * @property int $idCurso
 * @property Curso $curso
 * @property Cadeira[] $cadeiras
 */
class planocurricular extends Model
{
	protected $table = 'planocurricular';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['codigo', 'idCurso'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function curso()
    {
        return $this->belongsTo(curso::class, 'idCurso');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cadeiras()
    {
        return $this->hasMany(cadeira::class, 'idPlanoCurricular');
    }
}
