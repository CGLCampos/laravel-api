<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\LivroReservado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LivroController extends Controller
{
    
    public function index(Request $request) {
        try{
            return Livro::where('excluido', false)
                ->with('categoria')
                ->paginate($request->per_page);
        }
        catch (\Exception $e) 
        {
            return response()->json(['erro' => $e->getMessage()], 400);
        }
    }
    
    public function reservados(Request $request) {
        try {
            return Livro::where('excluido', false)
                ->where('reservado', true)
                ->with('categoria')
                ->paginate($request->per_page);

        }
        catch (\Exception $e) 
        {
            return response()->json(['erro' => $e->getMessage()], 400);
        }
    }
    
    public function disponiveis(Request $request) {
        try {
            return Livro::where('excluido', false)
                ->where('reservado', false)
                ->with('categoria')
                ->paginate($request->per_page);

        }
        catch (\Exception $e) 
        {
            return response()->json(['erro' => $e->getMessage()], 400);
        }
    }
    
    public function store(Request $request) {
        try {
            $this->validate($request, [
                'titulo' => 'required',
                'autor' => 'required',
                'editora' => 'required',
                'idioma' => 'required',
                'data_publicacao' => 'required|date_format:m/Y',
                'categoria_id' => 'required'
            ]);
    
            return response()
                ->json(
                    Livro::create([
                        'titulo' => $request->titulo,
                        'autor' => $request->autor,
                        'editora' => $request->editora,
                        'idioma' => $request->idioma,
                        'data_publicacao' => $request->data_publicacao,
                        'categoria_id' => $request->categoria_id,
                    ]), 
                    201
                );

        }
        catch (ValidationException $e) 
        {
            return response()->json($e->response->original, 422);
        }
        catch (\Exception $e) 
        {
            return response()->json(['erro' => $e->getMessage()], 400);
        }
    }

    public function show(int $id)
    {
        try {
            $livro = Livro::with('categoria')->find($id);
            if(is_null($livro) || $livro->excluido) {
                return response()->json([
                    'erro' => 'Livro não encontrado'
                ], 404);
            }
            return response()->json($livro);

        }
        catch (\Exception $e) 
        {
            return response()->json(['erro' => $e->getMessage()], 400);
        }
    }

    public function update(int $id, Request $request)
    {
        try {
            $this->validate($request, [
                'titulo' => 'required',
                'autor' => 'required',
                'editora' => 'required',
                'idioma' => 'required',
                'data_publicacao' => 'required|date_format:m/Y',
                'categoria_id' => 'required'
            ]);
    
            $livro = Livro::find($id);
            if(is_null($livro) || $livro->excluido) {
                return response()->json([
                    'erro' => 'Livro não encontrado'
                ], 404);
            }
    
            $livro->fill([
                'titulo' => $request->titulo,
                'autor' => $request->autor,
                'editora' => $request->editora,
                'idioma' => $request->idioma,
                'data_publicacao' => $request->data_publicacao,
                'categoria_id' => $request->categoria_id,
            ]);
    
            $livro->save();
    
            return $livro;

        }
        catch (ValidationException $e) 
        {
            return response()->json($e->response->original, 422);
        }
        catch (\Exception $e) 
        {
            return response()->json(['erro' => $e->getMessage()], 400);
        }
    }

    public function destroy(int $id)
    {
        try {
            $livro = Livro::find($id);
            if(is_null($livro)) {
                return response()->json([
                    'erro' => 'Livro não encontrado'
                ], 404);
            }
    
            if($livro->reservado == true) {
                return response()->json([
                    'erro' => 'O livro está reservado e não pode ser removido'
                ], 400);
            }
    
            if(LivroReservado::query()->where('livro_id', $id)) {
                $livro->excluido = true;
                $livro->save();
    
                return response()->json([
                    'sucesso' => 'Livro removido com sucesso'
                ], 200);
            }
    
            $livro->delete();
            return response()->json([
                'sucesso' => 'Livro removido com sucesso'
            ], 200);

        }
        catch (\Exception $e) 
        {
            return response()->json(['erro' => $e->getMessage()], 400);
        }

    }
}