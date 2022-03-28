<?php

namespace App\Models;

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
     * @var array
     */
    protected $fillable = ['idCadeira', 'idUtilizador','nrinscricoes'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cadeira()
    {
        return $this->belongsTo(cadeira::class, 'idCadeira');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function utilizador()
    {
        return $this->belongsTo(utilizador::class, 'idUtilizador');
    }
}
