<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $codigo
 * @property integer $ano
 * @property integer $semestre
 * @property string $tipo
 * @property string $nome
 * @property string $abreviatura
 * @property int $idCurso
 * @property string $anoletivo
 * @property Curso $curso
 * @property Inscricaouc[] $inscricaoucs
 * @property Pedidosuc[] $pedidosucs
 * @property Turno[] $turnos
 */
class Cadeira extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'cadeira';

    /**
     * @var array
     */
    protected $fillable = ['codigo', 'ano', 'semestre', 'nome', 'abreviatura', 'idCurso'];

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
    public function inscricaoucs()
    {
        return $this->hasMany(Inscricaoucs::class, 'idCadeira');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pedidosucs()
    {
        return $this->hasMany(Pedidosucs::class, 'idCadeira');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function turnos()
    {
        return $this->hasMany(Turno::class, 'idCadeira')->orderBy('tipo', 'DESC')->orderBy('numero', 'ASC');
    }

    public function turnosvisiveis()
    {
        return $this->hasMany(Turno::class, 'idCadeira')->WHERE('visivel', 1)->orderBy('tipo', 'DESC')->orderBy('numero', 'ASC');
    }
}
