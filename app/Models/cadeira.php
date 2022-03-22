<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $codigo
 * @property string $nome
 * @property string $abreviatura
 * @property int $idPlanoCurricular
 * @property Planocurricular $planocurricular
 * @property Pedidosuc[] $pedidosucs
 * @property Turno[] $turnos
 */
class cadeira extends Model
{
    protected $table = 'cadeira';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['codigo', 'nome', 'abreviatura', 'idPlanoCurricular'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function planocurricular()
    {
        return $this->belongsTo(planocurricular::class, 'idPlanoCurricular');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pedidosucs()
    {
        return $this->hasMany(pedidosucs::class, 'idCadeira');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function turnos()
    {
        return $this->hasMany(turno::class, 'idCadeira');
    }
}
