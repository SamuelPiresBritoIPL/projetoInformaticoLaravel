<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $idCadeira
 * @property int $idPedidos
 * @property int $aceite    0 => nao aceite || 1 => aceite
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Cadeira $cadeira
 * @property Pedido $pedido
 */
class Pedidosucs extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['idCadeira', 'idPedidos', 'aceite', 'created_at', 'updated_at', 'deleted_at'];

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
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'idPedidos');
    }
}
