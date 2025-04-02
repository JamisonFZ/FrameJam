<?php

use FrameJam\Core\Router;

// Rotas da aplicação
Router::get('/', 'HomeController@index');
Router::get('/sobre', 'HomeController@sobre');
Router::get('/contato', 'HomeController@contato');
Router::post('/contato', 'HomeController@enviarContato');

// Rotas de usuários
Router::get('/usuarios', 'UserController@index');
Router::get('/usuarios/{id}', 'UserController@show');
Router::get('/usuarios/criar', 'UserController@create');
Router::post('/usuarios', 'UserController@store');
Router::get('/usuarios/{id}/editar', 'UserController@edit');
Router::put('/usuarios/{id}', 'UserController@update');
Router::delete('/usuarios/{id}', 'UserController@destroy');

// Rotas de produtos
Router::get('/produtos', 'ProductController@index');
Router::get('/produtos/{id}', 'ProductController@show');
Router::get('/produtos/criar', 'ProductController@create');
Router::post('/produtos', 'ProductController@store');
Router::get('/produtos/{id}/editar', 'ProductController@edit');
Router::put('/produtos/{id}', 'ProductController@update');
Router::delete('/produtos/{id}', 'ProductController@destroy'); 