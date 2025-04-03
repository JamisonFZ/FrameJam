<?php

use FrameJam\Core\Application;

$router = Application::getInstance()->getRouter();

// Rotas da aplicação
$router->get('/', 'FrameJam\Controllers\HomeController@index');
$router->get('/sobre', 'FrameJam\Controllers\HomeController@sobre');
$router->get('/contato', 'FrameJam\Controllers\HomeController@contato');
$router->post('/contato', 'FrameJam\Controllers\HomeController@enviarContato');

// Rotas de usuários
$router->get('/usuarios', 'FrameJam\Controllers\UserController@index');
$router->get('/usuarios/{id}', 'FrameJam\Controllers\UserController@show');
$router->get('/usuarios/criar', 'FrameJam\Controllers\UserController@create');
$router->post('/usuarios', 'FrameJam\Controllers\UserController@store');
$router->get('/usuarios/{id}/editar', 'FrameJam\Controllers\UserController@edit');
$router->put('/usuarios/{id}', 'FrameJam\Controllers\UserController@update');
$router->delete('/usuarios/{id}', 'FrameJam\Controllers\UserController@destroy');

// Rotas de produtos
$router->get('/produtos', 'FrameJam\Controllers\ProductController@index');
$router->get('/produtos/{id}', 'FrameJam\Controllers\ProductController@show');
$router->get('/produtos/criar', 'FrameJam\Controllers\ProductController@create');
$router->post('/produtos', 'FrameJam\Controllers\ProductController@store');
$router->get('/produtos/{id}/editar', 'FrameJam\Controllers\ProductController@edit');
$router->put('/produtos/{id}', 'FrameJam\Controllers\ProductController@update');
$router->delete('/produtos/{id}', 'FrameJam\Controllers\ProductController@destroy'); 