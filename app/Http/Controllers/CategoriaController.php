<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Http\Request;
use App\Models\Categoria;


class CategoriaController
{
    
    public function index(Request $request) {
        return Categoria::all();
    }
    
    public function store(Request $request) {
        return response()
            ->json(
                Categoria::create($request->all()), 
                201
            );
    }

    public function destroy(int $id)
    {
        $qtdeRecursosRemovidos = Categoria::destroy($id);
        if($qtdeRecursosRemovidos === 0){
            return response()->json([
                'erro' => 'Categoria não encontrada'
            ], 404);
        }
        
        return response()->json([
            'sucesso' => 'Categoria removida com sucesso'
        ], 204);
    }

}