<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aluno extends Model {

    public $timestamps = false;
    
    protected $fillable = [
        'nome',
        'data_nascimento',
        'turma',
        'user_id'
    ];

    protected $hidden = [
        'user_id'
    ];

    // protected $appends = ['links'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class)->with('livrosReservados');
    }

}