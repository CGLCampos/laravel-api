<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use TheSeer\Tokenizer\Exception;
use Illuminate\Validation\ValidationException;
use App\Models\Role;


class AlunoController extends Controller
{
    public function index(Request $request) {
        return Aluno::with("user")->paginate($request->per_page);
    }
    
    public function store(Request $request) {
        try { 

            $this->validate($request, [
                'nome' => 'required',
                'data_nascimento' => 'required|date_format:d/m/Y|before:today',
                'turma' => 'required|regex:/^\d{1}-[A-Z]{1}$/',
                'email' => 'required|email',
                'senha' => 'required|min:6|confirmed',
            ]);

            DB::beginTransaction();

            if(!is_null(User::where('email', $request->email)->first())){
                return response()->json([
                    'email' => 'E-mail informado já está cadastrado'
                ], 404);
            }

            $user = User::create([
                'email' => $request->email,
                'senha' => Hash::make($request->senha),
            ]);
            $user->roles()->attach(Role::where('nome', 'USER_ROLE')->first());

            
            $aluno = Aluno::create([
                'nome' => $request->nome,
                'data_nascimento' => $request->data_nascimento,
                'turma' => $request->turma,
                'user_id' => $user->id,
            ]);

            //incluir rotina de envio de email de confirmação

            DB::commit();

            return response()->json($aluno, 201);
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
    
    public function show(int $id)
    {
        try
        {
            $aluno = Aluno::with('user', 'reservas')->find($id);

            if(is_null($aluno)) {
                return response()->json([
                    'erro' => 'Aluno não encontrado'
                ], 404);
            }
            return response()->json($aluno);
        }
        catch (\Exception $e) 
        {
            return response()->json(['erro' => $e->getMessage()], 400);
        }
    }
    
    public function perfil(Request $request)
    {
        try{
            $userLogado = TokenController::getUser($request);
            $aluno = Aluno::where('user_id', $userLogado->id)->with('user', 'reservas')->first();
    
            if(is_null($aluno)) {
                return response()->json([
                    'erro' => 'Aluno não encontrado'
                ], 404);
            }
            return response()->json($aluno);
        }
        catch (\Exception $e) 
        {
            return response()->json(['erro' => $e->getMessage()], 400);
        }
    }

    public function update(int $id, Request $request)
    {
        try{
            $this->validate($request, [
                'nome' => 'required',
                'data_nascimento' => 'required|date_format:d/m/Y|before:today',
                'turma' => 'required|regex:/^\d{1}-[A-Z]{1}$/',
            ]);
    
            $aluno = Aluno::find($id);
            if(is_null($aluno)) {
                return response()->json([
                    'erro' => 'Aluno não encontrado'
                ], 404);
            }
    
            $aluno->fill([
                'nome' => $request->nome,
                'data_nascimento' => $request->data_nascimento,
                'turma' => $request->turma
            ]);
            $aluno->save();
    
            return $aluno;
        }
        catch (ValidationException $e) 
        {
            DB::rollback();
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
            DB::beginTransaction();

            $aluno = Aluno::find($id);
            if(is_null($aluno)) {
                throw new Exception('Aluno não encontrado');
            }

            if(count($aluno->reservas) != 0) {
                return response()->json([
                    'erro' => 'O aluno possui reservas e não pode ser removido'
                ], 400);
            }

            $aluno->user->delete();

            DB::commit();

            return response()->json([
                'sucesso' => 'Aluno removido com sucesso'
            ], 200); 
        }
        
        catch (\Exception $e) 
        {
            DB::rollback();
            return response()->json([
                'erro' => $e->getMessage(),
            ], 400);
        }
    }


}