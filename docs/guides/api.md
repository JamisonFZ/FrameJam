# API REST

O FrameJam fornece um sistema completo para desenvolvimento de APIs RESTful, incluindo autenticação, validação e respostas padronizadas.

## Estrutura Básica

### Controller API Base

```php
namespace App\Controllers\Api;

use FrameJam\Core\Api\ApiController;

class UserController extends ApiController
{
    public function index()
    {
        $users = User::all();
        return $this->jsonResponse($users);
    }
}
```

### Rotas API

```php
// routes/api.php
$router->group(['prefix' => 'api', 'middleware' => 'api'], function($router) {
    // Rotas públicas
    $router->post('/login', 'Api\AuthController@login');
    $router->post('/register', 'Api\AuthController@register');
    
    // Rotas protegidas
    $router->group(['middleware' => 'auth:api'], function($router) {
        $router->resource('users', 'Api\UserController');
        $router->resource('posts', 'Api\PostController');
    });
});
```

## Autenticação

### Configuração

```php
// config/auth.php
return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],
    'guards' => [
        'api' => [
            'driver' => 'token',
            'provider' => 'users',
        ],
    ],
];
```

### Login

```php
namespace App\Controllers\Api;

use FrameJam\Core\Api\ApiController;

class AuthController extends ApiController
{
    public function login()
    {
        $credentials = $this->request->getPost();
        
        if (!$this->auth->attempt($credentials)) {
            return $this->errorResponse('Credenciais inválidas', 401);
        }
        
        $user = $this->auth->user();
        $token = $this->auth->generateToken($user->id);
        
        return $this->successResponse([
            'token' => $token,
            'user' => $user
        ]);
    }
}
```

### Proteção de Rotas

```php
// Middleware de autenticação
$router->get('/profile', 'Api\UserController@profile')
    ->middleware('auth:api');
```

## Respostas

### Formato Padrão

```php
// Sucesso
return $this->successResponse($data, 'Operação realizada com sucesso');

// Erro
return $this->errorResponse('Mensagem de erro', 400);

// Paginação
return $this->paginatedResponse($data, $total, $page, $perPage);
```

### Exemplo de Respostas

```json
// Sucesso
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe"
    },
    "message": "Operação realizada com sucesso"
}

// Erro
{
    "success": false,
    "message": "Mensagem de erro",
    "errors": {
        "email": ["O campo email é obrigatório"]
    }
}

// Paginação
{
    "success": true,
    "data": [...],
    "pagination": {
        "total": 100,
        "per_page": 15,
        "current_page": 1,
        "last_page": 7
    }
}
```

## Validação

### Validação de Requisições

```php
namespace App\Controllers\Api;

use FrameJam\Core\Api\ApiController;

class UserController extends ApiController
{
    public function store()
    {
        $data = $this->request->getPost();
        
        // Validação
        $validator = $this->validate($data, [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse('Dados inválidos', 422, $validator->errors());
        }
        
        // Criar usuário
        $user = User::create($data);
        return $this->successResponse($user);
    }
}
```

## Exemplos Práticos

### CRUD Completo

```php
namespace App\Controllers\Api;

use FrameJam\Core\Api\ApiController;

class PostController extends ApiController
{
    public function index()
    {
        $posts = Post::with('author')->paginate(15);
        return $this->paginatedResponse($posts, $posts->total(), $posts->currentPage(), 15);
    }
    
    public function store()
    {
        $data = $this->request->getPost();
        
        $validator = $this->validate($data, [
            'title' => 'required|min:3',
            'content' => 'required|min:10'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse('Dados inválidos', 422, $validator->errors());
        }
        
        $data['user_id'] = $this->auth->id();
        $post = Post::create($data);
        
        return $this->successResponse($post);
    }
    
    public function show($id)
    {
        $post = Post::with('author')->find($id);
        
        if (!$post) {
            return $this->errorResponse('Post não encontrado', 404);
        }
        
        return $this->successResponse($post);
    }
    
    public function update($id)
    {
        $post = Post::find($id);
        
        if (!$post) {
            return $this->errorResponse('Post não encontrado', 404);
        }
        
        if ($post->user_id !== $this->auth->id()) {
            return $this->errorResponse('Não autorizado', 403);
        }
        
        $data = $this->request->getPost();
        
        $validator = $this->validate($data, [
            'title' => 'required|min:3',
            'content' => 'required|min:10'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse('Dados inválidos', 422, $validator->errors());
        }
        
        $post->update($data);
        return $this->successResponse($post);
    }
    
    public function destroy($id)
    {
        $post = Post::find($id);
        
        if (!$post) {
            return $this->errorResponse('Post não encontrado', 404);
        }
        
        if ($post->user_id !== $this->auth->id()) {
            return $this->errorResponse('Não autorizado', 403);
        }
        
        $post->delete();
        return $this->successResponse(null, 'Post removido com sucesso');
    }
}
```

## Boas Práticas

1. **Organização**
   - Use versionamento de API
   - Mantenha controllers focados
   - Documente endpoints

2. **Segurança**
   - Use autenticação em todas as rotas sensíveis
   - Valide todas as entradas
   - Implemente rate limiting

3. **Performance**
   - Use cache quando apropriado
   - Otimize queries
   - Implemente paginação

4. **Manutenção**
   - Mantenha respostas consistentes
   - Use códigos HTTP apropriados
   - Documente mudanças na API 