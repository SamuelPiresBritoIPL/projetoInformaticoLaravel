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

    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = ['id','codigo', 'nome', 'abreviatura'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aberturas()
    {
        return $this->hasMany(Aberturas::class, 'idCurso')->orderBy('ano', 'ASC');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cadeiras()
    {
        return $this->hasMany(Cadeira::class, 'idCurso');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function coordenadors()
    {
        return $this->hasMany(Coordenador::class, 'idCurso');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function utilizadors()
    {
        return $this->hasMany(Utilizador::class, 'idCurso');
    }
}
