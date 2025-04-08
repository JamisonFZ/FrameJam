# FrameJam Framework

Um framework PHP minimalista e extensível, construído com os componentes essenciais para desenvolvimento web moderno.

## Características

- Sistema de Rotas Simples e Flexível
- Autoloading PSR-4
- Controladores MVC
- Sistema de Templates com Twig
- ORM Simples
- Sistema de Middleware
- Gerenciamento de Configurações
- Suporte a Variáveis de Ambiente

## Requisitos

- PHP 8.1 ou superior
- Composer
- MySQL 5.7 ou superior

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/framejam.git
```

2. Instale as dependências:
```bash
composer install
```

3. Copie o arquivo de ambiente:
```bash
cp .env.example .env
```

4. Configure suas variáveis de ambiente no arquivo `.env`

5. Inicie o servidor de desenvolvimento:
```bash
php -S localhost:8000 -t public
```

## Estrutura de Diretórios

```
framejam/
├── public/             # Ponto de entrada da aplicação
├── resources/          # Views e assets
├── routes/             # Definição de rotas
├── src/                # Código fonte do framework
│   ├── Commands/       # Comandos para aplicação
│   ├── Controllers/    # Controladores da aplicação
│   ├── Core/           # Componentes principais do framework
│   └── Database/       # Modelos da aplicação
├── storage/            # Arquivos de cache e logs
└── vendor/             # Dependências do Composer
```

## Uso Básico

### Definindo Rotas

```php
// routes/web.php
$router->get('/', 'HomeController@index');
$router->get('/users/{id}', 'UserController@show');
```

### Criando um Controlador

```php
namespace FrameJam\Controllers;

use FrameJam\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return $this->view('home/index.twig', [
            'title' => 'Bem-vindo'
        ]);
    }
}
```

### Criando um Modelo

```php
namespace FrameJam\Models;

use FrameJam\Core\Model;

class User extends Model
{
    protected $fillable = ['name', 'email'];
}
```

## Contribuindo

Contribuições são bem-vindas! Por favor, leia as diretrizes de contribuição antes de enviar um pull request.

## Licença

Este projeto está licenciado sob a licença MIT. 