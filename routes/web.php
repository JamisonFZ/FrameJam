<?php

use FrameJam\Core\Application;

$router = Application::getInstance()->getRouter();

// Rota básica
$router->get('/', 'HomeController@index');

// Rotas com parâmetros
$router->get('/users/{id}', 'UserController@show');
$router->post('/users', 'UserController@store');

// Exemplo de rota com middleware
$router->get('/admin', 'AdminController@index')->middleware('auth');

// Exemplo de rota com callback
$router->get('/about', function() {
    return 'Sobre nós';
}); 