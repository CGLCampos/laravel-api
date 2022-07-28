<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

    public $timestamps = false;
    
    protected $fillable = ['nome', 'descricao'];

    protected $hidden = [
        'id', 'pivot',
    ];

    public function users() {
        return $this->belongsToMany(User::class, 'user_role', 'role_id', 'user_id');
    }

}