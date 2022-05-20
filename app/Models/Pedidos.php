<?php

namespace App\Models;

use Hamcrest\Util;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $idUtilizador
 * @property int $idAnoLetivo
 * @property int $idCurso
 * @property int $estado   0 => sem alteracoes para se fazer || 1 => pedido de ucs pendente || 2 => pedido aceite por coordenador || 3 => pedido rejeitado pelo coordenador || 4 => parcialmente aceite
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
    protected $fillable = ['idUtilizador', 'idAnoletivo', 'idCurso', 'semestre', 'estado', 'descricao', 'created_at', 'updated_at', 'deleted_at'];

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
        return $this->belongsTo(Anoletivo::class, 'idAnoletivo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'idCurso');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pedidosucs()
    {
        return $this->hasMany(Pedidosucs::class, 'idPedidos');
    }
}
