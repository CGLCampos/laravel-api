<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categorias = [
            'Não Informado', 
            'Romance', 
            'Infanto-juvenil', 
            'Educativo', 
            'Fantasia',
            'Suspense',
            'Ficção Científica',
            'Aventura',
            'Infantil',
            'Auto-ajuda',
        ];

        foreach ($categorias as $categoria) {
            Categoria::create(["nome" => $categoria]);
        }
    }
}