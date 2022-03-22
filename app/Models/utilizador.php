<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $numero
 * @property string $email
 * @property string $nome
 * @property integer $tipo
 * @property int $idCurso
 * @property Curso $curso
 * @property Abertura[] $aberturas
 * @property Coordenador[] $coordenadors
 * @property Inscricao[] $inscricaos
 * @property Log[] $logs
 * @property Pedido[] $pedidos
 */
class utilizador extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'utilizador';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['numero', 'email', 'nome', 'tipo', 'idCurso'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function curso()
    {
        return $this->belongsTo('App\Curso', 'idCurso');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aberturas()
    {
        return $this->hasMany('App\Abertura', 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function coordenadors()
    {
        return $this->hasMany('App\Coordenador', 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inscricaos()
    {
        return $this->hasMany('App\Inscricao', 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany('App\Log', 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pedidos()
    {
        return $this->hasMany('App\Pedido', 'idUtilizador');
    }
}
