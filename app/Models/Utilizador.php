<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int $id
 * @property int $numero
 * @property string $email
 * @property string $nome
 * @property string $login
 * @property integer $tipo => 0 - estudante | 1 - professor | 2 - coordenador | 3 - admin
 * @property int $idCurso
 * @property Curso $curso
 * @property Abertura[] $aberturas
 * @property Coordenador[] $coordenadors
 * @property Inscricao[] $inscricaos
 * @property Inscricaouc[] $inscricaoucs
 * @property Log[] $logs
 * @property Pedido[] $pedidos
 */
class Utilizador extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'utilizador';

    /**
     * @var array
     */
    protected $fillable = ['numero', 'email', 'nome', 'login', 'tipo', 'password', 'idCurso'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
    public function aberturas()
    {
        return $this->hasMany(Aberturas::class, 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function coordenadors()
    {
        return $this->hasMany(Coordenador::class, 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inscricaos()
    {
        return $this->hasMany(Inscricao::class, 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inscricaoucs()
    {
        return $this->hasMany(Inscricaoucs::class, 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany(Logs::class, 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pedidos()
    {
        return $this->hasMany(Pedidos::class, 'idUtilizador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function turnos()
    {
        return $this->hasMany(Turno::class, 'idProfessor');
    }

    public function isAdmin(){
        if($this->tipo == 3)
            return true;
        return false;
    }

    public function isCoordenador(){
        if($this->tipo == 2 || count($this->coordenadors) != 0)
            return true;
        return false;
    }

    public function isProfessor(){
        if($this->tipo == 1)
            return true;
        return false;
    }

    public function isEstudante(){
        if($this->tipo == 0)
            return true;
        return false;
    }

    public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = bcrypt($value);
	}
}
