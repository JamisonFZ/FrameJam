# Sistema de Rotas

O sistema de rotas do FrameJam é flexível e poderoso, permitindo definir rotas para diferentes métodos HTTP e grupos de rotas.

## Definição de Rotas

As rotas são definidas no arquivo `routes/web.php` para rotas web e `routes/api.php` para rotas de API.

### Rotas Básicas

```php
// Rota GET simples
$router->get('/home', 'HomeController@index');

// Rota POST com parâmetros
$router->post('/users/{id}', 'UserController@update');

// Rota com múltiplos métodos
$router->match(['GET', 'POST'], '/users', 'UserController@handle');
```

### Parâmetros de Rota

```php
// Parâmetros obrigatórios
$router->get('/users/{id}', 'UserController@show');

// Parâmetros opcionais
$router->get('/users/{id?}', 'UserController@show');

// Parâmetros com restrições
$router->get('/users/{id}', 'UserController@show')->where('id', '[0-9]+');
```

### Grupos de Rotas

```php
// Grupo com prefixo
$router->group('/admin', function($router) {
    $router->get('/users', 'Admin\UserController@index');
    $router->get('/settings', 'Admin\SettingController@index');
});

// Grupo com middleware
$router->group(['middleware' => 'auth'], function($router) {
    $router->get('/profile', 'UserController@profile');
    $router->post('/settings', 'UserController@updateSettings');
});

// Grupo com namespace
$router->group(['namespace' => 'Admin'], function($router) {
    $router->get('/dashboard', 'DashboardController@index');
});
```

## Middleware

### Aplicando Middleware

```php
// Middleware em uma única rota
$router->get('/admin', 'AdminController@index')
    ->middleware('auth');

// Múltiplos middleware
$router->get('/admin', 'AdminController@index')
    ->middleware(['auth', 'admin']);
```

### Middleware com Parâmetros

```php
// Middleware com parâmetros
$router->get('/users/{id}', 'UserController@show')
    ->middleware('can:view,user');
```

## Controllers

### Formato Básico

```php
// Controller@método
$router->get('/users', 'UserController@index');

// Controller com namespace completo
$router->get('/users', 'App\Controllers\UserController@index');
```

### Controllers de Recurso

```php
// Define todas as rotas RESTful
$router->resource('users', 'UserController');

// Define rotas específicas
$router->resource('users', 'UserController', [
    'only' => ['index', 'show', 'store']
]);
```

## Redirecionamentos

```php
// Redirecionamento simples
$router->redirect('/home', '/dashboard');

// Redirecionamento com status
$router->redirect('/home', '/dashboard', 301);
```

## Exemplos Práticos

### Blog

```php
// Rotas do blog
$router->group(['prefix' => 'blog'], function($router) {
    $router->get('/', 'BlogController@index');
    $router->get('/{slug}', 'BlogController@show');
    $router->get('/category/{category}', 'BlogController@category');
    
    // Rotas protegidas
    $router->group(['middleware' => 'auth'], function($router) {
        $router->get('/create', 'BlogController@create');
        $router->post('/store', 'BlogController@store');
        $router->get('/{id}/edit', 'BlogController@edit');
        $router->put('/{id}', 'BlogController@update');
        $router->delete('/{id}', 'BlogController@destroy');
    });
});
```

### API REST

```php
// Rotas da API
$router->group(['prefix' => 'api', 'middleware' => 'api'], function($router) {
    // Autenticação
    $router->post('/login', 'Api\AuthController@login');
    $router->post('/register', 'Api\AuthController@register');
    
    // Rotas protegidas
    $router->group(['middleware' => 'auth:api'], function($router) {
        $router->resource('users', 'Api\UserController');
        $router->resource('posts', 'Api\PostController');
    });
});
```

## Boas Práticas

1. **Organização**
   - Agrupe rotas relacionadas
   - Use prefixos para namespaces
   - Mantenha as rotas organizadas por funcionalidade

2. **Segurança**
   - Aplique middleware de autenticação quando necessário
   - Use validação de parâmetros
   - Implemente rate limiting em APIs

3. **Performance**
   - Evite rotas muito complexas
   - Use cache de rotas em produção
   - Mantenha as rotas o mais simples possível

4. **Manutenção**
   - Documente rotas complexas
   - Use nomes descritivos
   - Mantenha a consistência no padrão de nomenclatura 