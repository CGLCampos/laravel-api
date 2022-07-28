<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\TokenController;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = TokenController::getUser($request);

        if($user === null) {
            return response()->json(['erro' => 'Acesso negado. Faça login para continuar.'], 401);
        }

        $actions = $request->route()[1];
        $roles = isset($actions['roles']) ? $actions['roles'] : null; 

        if($user->hasAnyrole($roles) || !$roles) {
            return $next($request);
        }
        return response()->json([
            'erro' => 'Acesso negado. Você não tem a permissão necessária para acessar esse conteúdo.'
        ], 401);
    }
}
