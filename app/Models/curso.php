<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $codigo
 * @property string $nome
 * @property string $abreviatura
 * @property Abertura[] $aberturas
 * @property Cadeira[] $cadeiras
 * @property Coordenador[] $coordenadors
 * @property Utilizador[] $utilizadors
 */
class Curso extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'curso';

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
    public function cadeiras()
    {
        return $this->hasMany(cadeira::class, 'idCurso');
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
    public function utilizadors()
    {
        return $this->hasMany(utilizador::class, 'idCurso');
    }
}
