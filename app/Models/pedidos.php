<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $idUtilizador
 * @property string $descricao
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Utilizador $utilizador
 * @property Pedidosuc[] $pedidosucs
 */
class Pedidos extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['idUtilizador', 'descricao', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function utilizador()
    {
        return $this->belongsTo(utilizador::class, 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pedidosucs()
    {
        return $this->hasMany(pedidosucs::class, 'idPedidos');
    }
}
