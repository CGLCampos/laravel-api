<?php

namespace App\Http\Services;
use App\Models\Aluno;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Exceptions\ResponseException;

class AlunoService {

    public function listar() {
        return Aluno::all();
    }

    public function listarComPaginacao($per_page) {
        return Aluno::with("user")->paginate($per_page);
    }

    public function salvar(Request $request)
    {
        DB::beginTransaction();

        if(!is_null(User::where('email', $request->email)->first())){
            throw new ResponseException('email', 'E-mail informado já está cadastrado');
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

        return $aluno;
    }

    public function buscar(int $id)
    {
        $aluno = Aluno::with('user', 'reservas')->find($id);

        if(is_null($aluno)) {
            throw new ResponseException('erro', 'Aluno não encontrado');
        }
        return $aluno;
    }

    public function buscarPerfil(int $id)
    {
        $aluno = Aluno::where('user_id', $id)->with('user', 'reservas')->first();
    
        if(is_null($aluno)) {
            throw new ResponseException('erro', 'Aluno não encontrado');
        }
        return $aluno;
    }

    public function editar(int $id, Request $request)
    {
        DB::beginTransaction();

        $aluno = $this->buscar($id);

        $aluno->fill([
            'nome' => $request->nome,
            'data_nascimento' => $request->data_nascimento,
            'turma' => $request->turma
        ]);
        $aluno->save();

        DB::commit();

        return $aluno;
    }

    public function excluir(int $id)
    {
        DB::beginTransaction();

        $aluno = $this->buscar($id);

        if(count($aluno->reservas) != 0) {
            throw new ResponseException('erro', 'O aluno possui reservas e não pode ser removido');
        }

        $aluno->user->delete();

        DB::commit();
    }

} 