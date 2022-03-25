<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $idCadeira
 * @property int $idUtilizador
 * @property Cadeira $cadeira
 * @property Utilizador $utilizador
 */
class inscricaoucs extends Model
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
    protected $fillable = ['idCadeira', 'idUtilizador'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cadeira()
    {
        return $this->belongsTo('App\Cadeira', 'idCadeira');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function utilizador()
    {
        return $this->belongsTo('App\Utilizador', 'idUtilizador');
    }
}
