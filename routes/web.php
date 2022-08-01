<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->options(
    '/{any:.*}', 
    [
        'middleware' => ['cors'], 
        function (){ 
            return response(['status' => 'success']); 
        }
    ]
);

$router->group(['middleware' => ['cors']], function() use($router) {
    $router->get('/', function () use ($router) {
        return $router->app->version();
    });

    $router->post('api/login', [
        'uses' => 'TokenController@gerarToken',
        'middleware' => ['cors']
    ]);

    $router->get('api/logged', [
        'uses' => 'TokenController@isLogged',
        'middleware' => ['cors']
    ]);

    $router->group(['prefix' => 'api/livros'], function() use($router) {
        $router->get('', 'LivroController@index');
        $router->get('disponivel', 'LivroController@disponiveis');
        $router->get('reservados', 'LivroController@reservados');
    });

    $router->post('api/alunos', 'AlunoController@store');

    $router->group(['prefix' => 'api', 'middleware' => ['auth']], function() use ($router) {

        $router->group(['prefix' => 'livros'], function() use($router) {

            $router->post('', [
                'uses' => 'LivroController@store',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE']
            ]);

            $router->get('{id}', [
                'uses' => 'LivroController@show',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE', 'USER_ROLE']
            ]);

            $router->put('{id}', [
                'uses' => 'LivroController@update',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE']
            ]);
            
            $router->delete('{id}', [
                'uses' => 'LivroController@destroy',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE']
            ]);
        });
        
        $router->group(['prefix' => 'categorias'], function() use($router) {

            $router->get('', [
                'uses' => 'CategoriaController@index',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE', 'USER_ROLE']
            ]);

            $router->post('', [
                'uses' => 'CategoriaController@store',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE']
            ]);

            $router->delete('{id}', [
                'uses' => 'CategoriaController@destroy',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE']
            ]);

        });
        
        $router->group(['prefix' => 'alunos'], function() use($router) {
            $router->get('', [
                'uses' => 'AlunoController@index',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE']
            ]);

            $router->get('listar', [
                'uses' => 'AlunoController@listar',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE']
            ]);

            $router->get('perfil', [
                'uses' => 'AlunoController@perfil',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE', 'USER_ROLE']
            ]);

            $router->get('{id}', [
                'uses' => 'AlunoController@show',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE']
            ]);

            $router->put('{id}', [
                'uses' => 'AlunoController@update',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE']
            ]);

            $router->delete('{id}', [
                'uses' => 'AlunoController@destroy',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE']
            ]);
        });
        
        $router->group(['prefix' => 'reservas'], function() use($router) {

            $router->get('', [
                'uses' => 'ReservaController@index',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE']
            ]);

            $router->post('', [
                'uses' => 'ReservaController@store',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE', 'USER_ROLE']
            ]);

            $router->get('finalizadas', [
                'uses' => 'ReservaController@finalizadas',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE']
            ]);

            $router->group(['prefix' => '{id}'], function() use($router) {

                $router->get('', [
                    'uses' => 'ReservaController@show',
                    'middleware' => ['roles'],
                    'roles' => ['ADMIN_ROLE', 'USER_ROLE']
                ]);

                $router->delete('', [
                    'uses' => 'ReservaController@destroy',
                    'middleware' => ['roles'],
                    'roles' => ['ADMIN_ROLE']
                ]);

                $router->put('devolver', [
                    'uses' => 'ReservaController@devolverTodosLivros',
                    'middleware' => ['roles'],
                    'roles' => ['ADMIN_ROLE']
                ]);
                
                $router->put('finalizar', [
                    'uses' => 'ReservaController@finalizarReserva',
                    'middleware' => ['roles'],
                    'roles' => ['ADMIN_ROLE']
                ]);

            });
            
            $router->put('devolver/{id}', [
                'uses' => 'ReservaController@devolverLivro',
                'middleware' => ['roles'],
                'roles' => ['ADMIN_ROLE']
            ]);
        });
    });
});
