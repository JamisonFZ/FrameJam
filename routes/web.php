<?php

use FrameJam\Core\Application;
use FrameJam\Core\Middleware\AuthMiddleware;

$router = Application::getInstance()->get('router');

// Rota básica
$router->get('/', 'HomeController@index');

// Rotas com parâmetros
$router->get('/users/{id}', 'UserController@show');
$router->post('/users', 'UserController@store');

// Rotas de autenticação
$router->get('/login', 'AuthController@showLoginForm');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// Rotas administrativas (protegidas por autenticação)
$router->group(['middleware' => AuthMiddleware::class], function($router) {
    $router->get('/admin', 'AdminController@index');
    $router->get('/admin/users', 'AdminController@users');
    $router->get('/admin/settings', 'AdminController@settings');
});

// Exemplo de rota com callback
$router->get('/about', function() {
    return 'Sobre nós';
}); 