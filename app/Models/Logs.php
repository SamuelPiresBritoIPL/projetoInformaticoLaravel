<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $descricao
 * @property string $tabela
 * @property int $idUtilizador
 * @property string $created_at
 * @property string $updated_at
 * @property Utilizador $utilizador
 */
class Logs extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['descricao', 'tabela', 'idUtilizador', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function utilizador()
    {
        return $this->belongsTo(Utilizador::class, 'idUtilizador');
    }
}
