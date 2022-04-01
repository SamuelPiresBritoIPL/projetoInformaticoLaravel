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
class Inscricaoucs extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['idCadeira', 'idUtilizador','idAnoletivo','nrinscricoes'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cadeira()
    {
        return $this->belongsTo(Cadeira::class, 'idCadeira');
    }

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
    public function anoletivo()
    {
        return $this->belongsTo(Anoletivo::class, 'idUtilizador');
    }
}
