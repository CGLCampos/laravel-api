<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livro extends Model {

    public $timestamps = false;
    protected $perPage = 10;

    protected $fillable = [
        'titulo',
        'autor', 
        'editora', 
        'idioma', 
        'data_publicacao', 
        'reservado', 
        'excluido', 
        'categoria_id', 
        'reserva_id'
    ];

    protected $hidden = [
        'categoria_id', 'excluido'
    ];

    // protected $appends = ['links'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

	function getReservadoAttribute($reservado) : bool {
		return $reservado;
	}

	function getExcluidoAttribute($excluido) : bool {
		return $excluido;
	}

    public function reservar($reserva_id)
    {
        $this->reserva_id = $reserva_id;
        $this->reservado = true;

        $this->save();
    }

    public function devolver()
    {
        $this->reserva_id = null;
        $this->reservado = false;
        
        $this->save();
    }

}