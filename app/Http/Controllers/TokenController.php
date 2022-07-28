<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenController extends Controller
{
    public function gerarToken(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'senha' => 'required'
        ]);

        $usuario = User::where('email', $request->email)->first();

        if(is_null($usuario) || !Hash::check($request->senha, $usuario->senha)) {
            return response()->json(['email' => 'Usuário ou senha inválidos'], 401);
        }

        $token = JWT::encode(['email' => $request->email], env('JWT_KEY'), 'HS256');

        return response()->json([
            'access_token' => $token
        ]);
    }
    
    public function isLogged(Request $request)
    {
        $user = $this->getUser($request);
        $logged = $user != null ? true : false;
        
        if($logged) {
            $roles = [];
            foreach ($user->roles as $role) {
                array_push($roles, $role->nome);
            }

            $user = [
                'email' => $user->email,
                'roles' => $roles
            ];
        }

        return \response()->json([
            'logged' => $logged,
            'user' => $user
        ]);
        
    }
    
    public function isAuthorized(Request $request)
    {
        $user = $this->getUser($request);
        $logged = $user != null ? true : false;
        
        if($logged) {
            $roles = [];
            foreach ($user->roles as $role) {
                array_push($roles, $role->nome);
            }

            $user = [
                'email' => $user->email,
                'roles' => $roles
            ];
        }

        return \response()->json([
            'logged' => $logged,
            'user' => $user
        ]);
        

    }
    public function options() {
        return response()->json(['status' => 'success']);
    }

    static function getUser(Request $request) {
        try {
            if (!$request->hasHeader('Authorization')) {
                return null;
            }
            $authorizationHeader = $request->header('Authorization');
            $token = str_replace('Bearer ', '', $authorizationHeader);

            $dadosAutenticacao = JWT::decode($token, new Key(env('JWT_KEY'), 'HS256'));
        
            return User::where('email', $dadosAutenticacao->email)->first();

        } catch (\Throwable $th) {
            return null;
            
        }
        
    }

    
}
