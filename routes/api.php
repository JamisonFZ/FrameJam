<?php

use FrameJam\Core\Application;

$router = Application::getInstance()->getRouter();

// Rotas da API
$router->get('/api/users', 'Api\UserController@index');
$router->get('/api/users/{id}', 'Api\UserController@show');
$router->post('/api/users', 'Api\UserController@store');
$router->put('/api/users/{id}', 'Api\UserController@update');
$router->delete('/api/users/{id}', 'Api\UserController@destroy');

// Produtos
$router->get('/api/products', 'Api\ProductController@index');
$router->get('/api/products/{id}', 'Api\ProductController@show');
$router->post('/api/products', 'Api\ProductController@store');
$router->put('/api/products/{id}', 'Api\ProductController@update');
$router->delete('/api/products/{id}', 'Api\ProductController@destroy');

// Autenticação
$router->post('/api/auth/login', 'Api\AuthController@login');
$router->post('/api/auth/register', 'Api\AuthController@register');
$router->post('/api/auth/logout', 'Api\AuthController@logout');
$router->get('/api/auth/user', 'Api\AuthController@user'); 