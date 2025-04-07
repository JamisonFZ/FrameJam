# Controllers

Os Controllers são a camada que gerencia a lógica de negócios da aplicação, processando requisições e retornando respostas apropriadas.

## Controller Base

Todos os controllers devem estender a classe base `FrameJam\Core\Controller`:

```php
use FrameJam\Core\Controller;

class UserController extends Controller
{
    public function index()
    {
        // Lógica do método
    }
}
```

## Tipos de Controllers

### Controller Web

```php
namespace App\Controllers;

use FrameJam\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return $this->view('home.index', [
            'title' => 'Página Inicial'
        ]);
    }
}
```

### Controller API

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

    public function store()
    {
        $data = $this->request->getPost();
        $user = User::create($data);
        return $this->successResponse($user);
    }
}
```

## Métodos do Controller

### Respostas

```php
// Retornar view
public function show($id)
{
    $user = User::find($id);
    return $this->view('users.show', ['user' => $user]);
}

// Retornar JSON
public function api($id)
{
    $user = User::find($id);
    return $this->jsonResponse($user);
}

// Redirecionar
public function store()
{
    // Lógica de criação
    return $this->redirect('/users');
}

// Download de arquivo
public function download()
{
    return $this->download('file.pdf', 'document.pdf');
}
```

### Validação

```php
public function store()
{
    $data = $this->request->getPost();
    
    // Validação básica
    if (empty($data['name']) || empty($data['email'])) {
        return $this->jsonResponse([
            'error' => 'Campos obrigatórios'
        ], 400);
    }
    
    // Criar usuário
    $user = User::create($data);
    return $this->jsonResponse($user);
}
```

## Exemplos Práticos

### CRUD Completo

```php
namespace App\Controllers;

use FrameJam\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return $this->view('users.index', ['users' => $users]);
    }

    public function create()
    {
        return $this->view('users.create');
    }

    public function store()
    {
        $data = $this->request->getPost();
        $user = User::create($data);
        return $this->redirect('/users');
    }

    public function show($id)
    {
        $user = User::find($id);
        return $this->view('users.show', ['user' => $user]);
    }

    public function edit($id)
    {
        $user = User::find($id);
        return $this->view('users.edit', ['user' => $user]);
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        $user = User::find($id);
        $user->update($data);
        return $this->redirect('/users');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return $this->redirect('/users');
    }
}
```

### API REST

```php
namespace App\Controllers\Api;

use FrameJam\Core\Api\ApiController;
use App\Models\User;

class UserController extends ApiController
{
    public function index()
    {
        $users = User::all();
        return $this->jsonResponse($users);
    }

    public function store()
    {
        $data = $this->request->getPost();
        
        try {
            $user = User::create($data);
            return $this->successResponse($user);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function show($id)
    {
        $user = User::find($id);
        return $this->jsonResponse($user);
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        $user = User::find($id);
        
        try {
            $user->update($data);
            return $this->successResponse($user);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return $this->successResponse(null, 'Usuário removido com sucesso');
    }
}
```

## Boas Práticas

1. **Organização**
   - Mantenha controllers pequenos e focados
   - Use injeção de dependência
   - Separe a lógica de negócios dos controllers

2. **Segurança**
   - Valide todas as entradas
   - Use middleware de autenticação
   - Sanitize dados antes de salvar

3. **Performance**
   - Evite queries N+1
   - Use cache quando apropriado
   - Otimize consultas ao banco de dados

4. **Manutenção**
   - Documente métodos complexos
   - Use nomes descritivos
   - Mantenha a consistência no padrão de nomenclatura 