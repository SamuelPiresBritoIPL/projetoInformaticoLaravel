<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $idCadeira
 * @property int $idPedidos
 * @property Cadeira $cadeira
 * @property Pedido $pedido
 */
class pedidosucs extends Model
{
    use SoftDeletes;
    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['idCadeira', 'idPedidos'];

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
    public function pedido()
    {
        return $this->belongsTo(pedidos::class, 'idPedidos');
    }
}
