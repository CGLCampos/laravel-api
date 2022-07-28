<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model {

    public $timestamps = false;
    protected $fillable = [
        'data_reserva',
        'data_finalizacao',
        'finalizado',
        'aluno_id'
    ];

    protected $hidden = [
        'aluno_id',
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

    public function livrosReservados()
    {
        return $this->hasMany(LivroReservado::class)->with('livro');
    }

	function getFinalizadoAttribute($finalizado) : bool {
		return $finalizado;
	}

}