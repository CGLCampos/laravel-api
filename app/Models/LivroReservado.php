<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivroReservado extends Model {

    public $timestamps = false;
    protected $table = 'livro_reservado';

    
    protected $fillable = [
        'data_devolucao',
        'devolvido',
        'livro_id',
        'reserva_id'
    ];

    protected $hidden = [
        'livro_id',
        'reserva_id'
    ];

    // protected $appends = ['links'];

    public function livro()
    {
        return $this->belongsTo(Livro::class)->with('categoria');
    }

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

	function getDevolvidoAttribute($devolvido) : bool {
		return $devolvido;
	}

    public function devolver()
    {
        $this->data_devolucao = date('d/m/Y');
        $this->devolvido = true;
        $this->livro->devolver();
        
        $this->save();
    }

}