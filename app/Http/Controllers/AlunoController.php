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
use App\Http\Services\AlunoService;
use App\Exceptions\ResponseException;


class AlunoController extends Controller
{
    private AlunoService $service;

    function __construct() {
        $this->service = new AlunoService();
    }

    public function index(Request $request) {
        return $this->service->listarComPaginacao($request->per_page);
    }

    public function listar(Request $request) {
        return $this->service->listar();
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

            return response()->json($this->service->salvar($request), 201);
        }
        catch (ValidationException $e) {
            DB::rollback();
            return response()->json($e->response->original, 422);
        }
        catch (ResponseException $e) {
            DB::rollback();
            return response()->json($e->getErro(), 422);
        }
        catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage(), 400);
        }
    }
    
    public function show(int $id) {
        try {
            return $this->service->buscar($id);
        }
        catch (ResponseException $e) {
            return response()->json($e->getErro(), 400);
        }
        catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 400);
        }
    }
    
    public function perfil(Request $request) {
        try {
            $userLogado = TokenController::getUser($request);
            $aluno = $this->service->buscarPerfil($userLogado->id);
            return response()->json($aluno);
        }
        catch (ResponseException $e) {
            return response()->json($e->getErro(), 400);
        }
        catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 400);
        }
    }

    public function update(int $id, Request $request) {
        try {
            $this->validate($request, [
                'nome' => 'required',
                'data_nascimento' => 'required|date_format:d/m/Y|before:today',
                'turma' => 'required|regex:/^\d{1}-[A-Z]{1}$/',
            ]);
    
            return $this->service->editar($id, $request);
        }
        catch (ValidationException $e) {
            DB::rollback();
            return response()->json($e->response->original, 422);
        }
        catch (ResponseException $e) {
            DB::rollback();
            return response()->json($e->getErro(), 422);
        }
        catch (\Exception $e) {
            DB::rollback();
            return response()->json(['erro' => $e->getMessage()], 400);
        }
    }

    public function destroy(int $id)
    {
        try { 
           $this->service->excluir($id);

            return response()->json([
                'sucesso' => 'Aluno removido com sucesso'
            ], 200); 
        }
        catch (ResponseException $e) {
            DB::rollback();
            return response()->json($e->getErro(), 400);
        }
        catch (\Exception $e) {
            DB::rollback();
            return response()->json(['erro' => $e->getMessage()], 400);
        }
    }


}