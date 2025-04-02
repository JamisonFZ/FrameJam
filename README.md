# FrameJam Framework

Um framework PHP simples e essencial, construído com foco em simplicidade e performance.

## Requisitos

- PHP 8.1 ou superior
- PDO PHP Extension
- Composer

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/framejam.git
```

2. Instale as dependências:
```bash
composer install
```

3. Configure o banco de dados no arquivo `config/database.php`

## Estrutura do Projeto

```
framejam/
├── config/             # Arquivos de configuração
├── src/               # Código fonte do framework
│   └── Core/          # Classes principais do framework
│       ├── Application.php
│       ├── Container.php
│       ├── Controller.php
│       ├── Database/
│       │   └── Model.php
│       ├── Middleware/
│       │   └── MiddlewareInterface.php
│       ├── Request.php
│       ├── Response.php
│       ├── Router.php
│       └── Config.php
├── views/             # Templates de visualização
└── public/            # Arquivos públicos
    └── index.php      # Ponto de entrada da aplicação
```

## Uso Básico

### Rotas

```php
use FrameJam\Core\Application;

$app = Application::getInstance();

$app->getRouter()->get('/', function($request) {
    return new Response('Hello World!');
});

$app->getRouter()->get('/users', 'UserController@index');
```

### Controladores

```php
use FrameJam\Core\Controller;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return $this->view('users.index', ['users' => $users]);
    }
}
```

### Modelos

```php
use FrameJam\Core\Database\Model;

class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];
}
```

### Middlewares

```php
use FrameJam\Core\Middleware\MiddlewareInterface;
use FrameJam\Core\Request;
use FrameJam\Core\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request): ?Response
    {
        if (!isset($_SESSION['user'])) {
            return new Response('Unauthorized', 401);
        }
        return null;
    }
}
```

### Views

```php
// views/users/index.php
<h1>Usuários</h1>
<ul>
    <?php foreach ($users as $user): ?>
        <li><?= htmlspecialchars($user->name) ?></li>
    <?php endforeach; ?>
</ul>
```

## Configuração

### Banco de Dados

Configure o banco de dados no arquivo `config/database.php`:

```php
return [
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'framejam',
    'username' => 'root',
    'password' => '',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
```

## Exemplo Completo

### Criando um CRUD de Usuários

1. Modelo:
```php
use FrameJam\Core\Database\Model;

class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];
}
```

2. Controlador:
```php
use FrameJam\Core\Controller;

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
        $user = new User([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
        ]);
        
        $user->save();
        
        return $this->redirect('/users');
    }

    public function edit($id)
    {
        $user = User::find($id);
        return $this->view('users.edit', ['user' => $user]);
    }

    public function update($id)
    {
        $user = User::find($id);
        $user->name = $this->request->getPost('name');
        $user->email = $this->request->getPost('email');
        $user->save();
        
        return $this->redirect('/users');
    }

    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();
        
        return $this->redirect('/users');
    }
}
```

3. Rotas:
```php
$router = Application::getInstance()->getRouter();

$router->get('/users', 'UserController@index');
$router->get('/users/create', 'UserController@create');
$router->post('/users', 'UserController@store');
$router->get('/users/{id}/edit', 'UserController@edit');
$router->put('/users/{id}', 'UserController@update');
$router->delete('/users/{id}', 'UserController@delete');
```

## Recursos Disponíveis

### Sistema de Rotas
- Suporte a rotas GET, POST, PUT e DELETE
- Parâmetros de rota com sintaxe {param}
- Middlewares por rota
- Handlers em closure ou controller@method

### Controladores
- Classe base Controller com métodos úteis
- Suporte a views
- Respostas JSON
- Redirecionamentos

### Modelos
- ORM simples e intuitivo
- CRUD básico (Create, Read, Update, Delete)
- Proteção contra mass assignment
- Campos ocultos
- Nome de tabela automático

### Middlewares
- Interface para criação de middlewares
- Suporte a múltiplos middlewares por rota
- Interrupção da requisição quando necessário

### Views
- Sistema de templates PHP
- Passagem de dados para views
- Escape automático de dados

### Configuração
- Carregamento automático de arquivos de configuração
- Acesso hierárquico às configurações
- Valores padrão

## Contribuindo

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes. 