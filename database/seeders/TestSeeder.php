<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Aluno;
use Illuminate\Support\Facades\Hash;
use App\Models\Livro;
use App\Models\Role;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        try {
            DB::beginTransaction();

            $role_user = Role::where('nome', 'USER_ROLE')->first();

            for ($i=1; $i <= 10; $i++) {
    
                $user = User::create([
                    'email' => 'aluno'.$i.'@escola.com',
                    'senha' => Hash::make('aluno'.$i),
                ]);
                $user->roles()->attach($role_user);
                
                Aluno::create([
                    'nome' => 'Aluno '.$i,
                    'data_nascimento' => '01/01/2010',
                    'turma' => '1-A',
                    'user_id' => $user->id,
                ]);
                
                Livro::create([
                    "titulo" => 'Livro '.$i,
                    "autor" => 'Autor '.$i,
                    "editora" => 'Editora '.$i,
                    "idioma" => 'Português',
                    "data_publicacao" => '01/2020',
                    "categoria_id" => $i,
                ]);

            }
    
            DB::commit();
        }

        catch (\Exception $e) 
        {
            DB::rollback();
        }
    }
}
