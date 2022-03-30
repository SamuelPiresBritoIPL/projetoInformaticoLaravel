<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $numero
 * @property string $email
 * @property string $nome
 * @property string $login
 * @property integer $tipo => 0 - estudante | 1 - professor | 2 - coordenador
 * @property int $idCurso
 * @property Curso $curso
 * @property Abertura[] $aberturas
 * @property Coordenador[] $coordenadors
 * @property Inscricao[] $inscricaos
 * @property Inscricaouc[] $inscricaoucs
 * @property Log[] $logs
 * @property Pedido[] $pedidos
 */
class Utilizador extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'utilizador';

    /**
     * @var array
     */
    protected $fillable = ['numero', 'email', 'nome', 'login', 'tipo', 'idCurso'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function curso()
    {
        return $this->belongsTo(curso::class, 'idCurso');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aberturas()
    {
        return $this->hasMany(aberturas::class, 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function coordenadors()
    {
        return $this->hasMany(coordenador::class, 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inscricaos()
    {
        return $this->hasMany(inscricao::class, 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inscricaoucs()
    {
        return $this->hasMany(inscricaoucs::class, 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany(logs::class, 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pedidos()
    {
        return $this->hasMany(pedido::class, 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function turnos()
    {
        return $this->hasMany(turno::class, 'idProfessor');
    }
}
