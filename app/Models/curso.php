<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $codigo
 * @property string $nome
 * @property string $abreviatura
 * @property Abertura[] $aberturas
 * @property Coordenador[] $coordenadors
 * @property Planocurricular[] $planocurriculars
 * @property Utilizador[] $utilizadors
 */
class curso extends Model
{
	protected $table = 'curso';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['codigo', 'nome', 'abreviatura'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aberturas()
    {
        return $this->hasMany(aberturas::class, 'idCurso');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function coordenadors()
    {
        return $this->hasMany(coordenador::class, 'idCurso');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planocurriculars()
    {
        return $this->hasMany(planocurricular::class, 'idCurso');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function utilizadors()
    {
        return $this->hasMany(utilizador::class, 'idCurso');
    }
}
