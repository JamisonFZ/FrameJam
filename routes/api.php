<?php

use FrameJam\Core\Router;

// Grupo de rotas da API
Router::group('/api', function() {
    // Usuários
    Router::get('/users', 'Api\UserController@index');
    Router::get('/users/{id}', 'Api\UserController@show');
    Router::post('/users', 'Api\UserController@store');
    Router::put('/users/{id}', 'Api\UserController@update');
    Router::delete('/users/{id}', 'Api\UserController@destroy');

    // Produtos
    Router::get('/products', 'Api\ProductController@index');
    Router::get('/products/{id}', 'Api\ProductController@show');
    Router::post('/products', 'Api\ProductController@store');
    Router::put('/products/{id}', 'Api\ProductController@update');
    Router::delete('/products/{id}', 'Api\ProductController@destroy');

    // Autenticação
    Router::post('/auth/login', 'Api\AuthController@login');
    Router::post('/auth/register', 'Api\AuthController@register');
    Router::post('/auth/logout', 'Api\AuthController@logout');
    Router::get('/auth/user', 'Api\AuthController@user');
}); 