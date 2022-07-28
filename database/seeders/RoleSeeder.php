<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $roles = [
            'ADMIN_ROLE' => 'Usuário administrador com acesso a todas as rotas',
            'USER_ROLE' => 'Um usuário comum, com acesso limitado',
        ];

        foreach ($roles as $chave => $valor) {
            Role::create([
                'nome' => $chave,
                'descricao' => $valor
            ]);
        }
        
    }
}
