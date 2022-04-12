<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $anoletivo
 */
class Anoletivo extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'anoletivo';

    /**
     * @var array
     */
    protected $fillable = ['id', 'anoletivo','semestreativo','ativo'];

   /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function turno()
    {
        return $this->hasMany(Turno::class, 'idTurno');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inscricaoucs()
    {
        return $this->hasMany(Inscricaoucs::class, 'idAnoletivo');
    }
}
