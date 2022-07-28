<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\LivroReservado;
use Illuminate\Support\Facades\DB;
use App\Models\Livro;
use App\Models\Aluno;
use Illuminate\Validation\ValidationException;


class ReservaController extends Controller
{
    public function index(Request $request) {
        return Reserva::where('finalizado', false)->with('aluno', 'livrosReservados')->paginate($request->per_page);
    }

    public function finalizadas(Request $request) {
        return Reserva::where('finalizado', true)->with('aluno', 'livrosReservados')->paginate($request->per_page);
    }
    
    public function store(Request $request) {
        try {

            $this->validate($request, [
                'aluno_id' => 'required',
                'livros_reservados' => 'required|array|min:1|max:3',
            ]);

            DB::beginTransaction();

            $aluno = Aluno::find($request->aluno_id);
            if(is_null($aluno)) {
                return response()->json([
                    'erro' => 'Aluno não encontrado'
                ], 404);
            }

            $userLogado = TokenController::getUser($request);

            // $userLogado->id != $aluno->user->id
            if($userLogado->id != $aluno->user->id 
                && !$userLogado->hasRole('ADMIN_ROLE')
                && $userLogado->hasRole('USER_ROLE')) {

                return response()->json([
                    'erro' => 'Acesso negado. Você não tem a permissão necessária para realizar essa ação.'
                ], 401);
            }

            $reserva_id = Reserva::create([
                'data_reserva' => date('d/m/Y'),
                'aluno_id' => $aluno->id
            ])->id;

            foreach ($request->livros_reservados as $livro_id) {

                $livro = Livro::find($livro_id);
                if(is_null($livro) || $livro->excluido) {
                    return response()->json([
                        'erro' => 'Livro não encontrado'
                    ], 404);
                }
    
                if($livro->reservado) {
                    return response()->json([
                        'erro' => 'O livro já está reservado'
                    ], 404);
                }
    
                $livro->reservar($reserva_id);
    
                LivroReservado::create([
                    'livro_id' => $livro->id,
                    'reserva_id' => $reserva_id,
                ]);
            }
    
            $reserva = Reserva::with('aluno', 'livrosReservados')->find($reserva_id);

            DB::commit();

            return response()->json($reserva, 201);

        } 
        catch (ValidationException $e) 
        {
            DB::rollback();
            return response()->json($e->response->original, 422);
        }
        catch (\Exception $e) 
        {
            DB::rollback();
            return response()->json($e->getTraceAsString(), 400);
        }

    }

    public function show(Request $request, int $id) {
        try{

            $reserva = Reserva::with('aluno', 'livrosReservados')->find($id);
            if(is_null($reserva)){
                return response()->json([
                    'erro' => 'Reserva não encontrada'
                ], 404);
            }

            $userLogado = TokenController::getUser($request);

            if($userLogado->email != $reserva->aluno->user->email
                && !$userLogado->hasRole('ADMIN_ROLE')
                && $userLogado->hasRole('USER_ROLE')) {

                return response()->json([
                    'erro' => 'Acesso negado. Você não tem a permissão necessária para acessar esse conteúdo.'
                ], 401);
            }

            return response()->json($reserva);

        } catch(\Exception $e) {
            return response()->json([
                'erro' => $e->getMessage()
            ]);
        }
    }

    public function devolverLivro(int $id) {
        try {
            DB::beginTransaction();

            $reservado = LivroReservado::find($id);
            if(is_null($reservado)){
                return response()->json([
                    'erro' => 'Livro não encontrado'
                ], 404);
            }

            $reservado->devolver();

            DB::commit();

            return response()->json([
                'sucesso' => 'Livro devolvido com sucesso'
            ], 200);

        } catch(\Exception $e) {
            DB::rollback();
            return response()->json([
                'erro' => $e->getMessage(),
            ], 400);
        }

    }

    public function devolverTodosLivros(int $id) {
        try {
            DB::beginTransaction();

            $reserva = Reserva::with('livrosReservados')->find($id);

            foreach($reserva->livrosReservados as $reservado){
                $reservado->devolver();
            }

            DB::commit();

            return response()->json([
                'sucesso' => 'Todos os livro foram devolvidos com sucesso'
            ], 200);
            
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json([
                'erro' => $e->getMessage(),
            ], 400);
        }
        
    }

    public function finalizarReserva(int $id) {
        try {

            $reserva = Reserva::with('livrosReservados')->find($id);
            if(is_null($reserva)){
                return response()->json([
                    'erro' => 'Reserva não encontrada'
                ], 404);
            }

            foreach($reserva->livrosReservados as $reservado){
                if(!$reservado->devolvido) {
                    return response()->json([
                        'erro' => 'Não é possível finalizar a reserva pois ela possui livros a devolver'
                    ], 400);
                }
            }

            if(!$reserva->finalizado){
                $reserva->data_finalizacao = date('d/m/Y');
                $reserva->finalizado = true;
    
                $reserva->save();
            }

            return response()->json([
                'sucesso' => 'Reserva finalizada com sucesso'
            ], 200);
            
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json([
                'erro' => $e->getMessage(),
            ], 400);
        }
        
    }

    public function destroy(int $id)
    {
        try {
            DB::beginTransaction();

            $reserva = Reserva::with('livrosReservados')->find($id);
            if(is_null($reserva)){
                return response()->json([
                    'erro' => 'Reserva não encontrada'
                ], 404);
            }

            foreach($reserva->livrosReservados as $reservado){
                if(!$reservado->devolvido) {
                    return response()->json([
                        'erro' => 'Não é possível remover a reserva pois ela possui livros a devolver'
                    ], 400);
                }
            }

            if(!$reserva->finalizado) {
                return response()->json([
                    'erro' => 'Não é possível remover a reserva pois ela não está finalizada'
                ], 400);
            }

            $reserva->delete();
    
            DB::commit();

            return response()->json([
                'sucesso' => 'Reserva removida com sucesso'
            ], 200);

        } catch(\Exception $e) {
            DB::rollback();
            return response()->json([
                'erro' => $e->getMessage(),
            ], 400);
        }

    }
}