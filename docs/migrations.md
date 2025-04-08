# Sistema de Migrações

O FrameJam possui um sistema de migrações para gerenciar a estrutura do banco de dados. Este documento explica como criar e executar migrações.

## Estrutura

As migrações são arquivos PHP localizados em `src/Database/Migrations/`. Cada migração representa uma alteração no banco de dados, como criação ou modificação de tabelas.

## Criando uma Migração

Para criar uma nova migração, crie uma classe que estenda `FrameJam\Core\Database\Migration`. A classe deve implementar três métodos:

1. `getTableName()`: Retorna o nome da tabela que será criada/modificada
2. `up()`: Define as alterações a serem aplicadas
3. `down()`: Define como reverter as alterações

Exemplo:

```php
<?php

namespace FrameJam\Database\Migrations;

use FrameJam\Core\Database\Migration;

class CreateUsersTable extends Migration
{
    public function getTableName(): string
    {
        return 'users';
    }

    public function up(): void
    {
        $this->schema
            ->id()
            ->string('name')
            ->string('email')->unique()
            ->string('password')
            ->boolean('active')->default('1')
            ->timestamps()
            ->create();
    }

    public function down(): void
    {
        $this->schema->drop();
    }
}
```

## Tipos de Colunas Disponíveis

O `SchemaBuilder` oferece vários métodos para definir colunas:

- `id(string $name = 'id')`: Cria uma coluna ID auto-incremento
- `string(string $name, int $length = 255)`: Cria uma coluna VARCHAR
- `text(string $name)`: Cria uma coluna TEXT
- `integer(string $name)`: Cria uma coluna INT
- `bigInteger(string $name)`: Cria uma coluna BIGINT
- `boolean(string $name)`: Cria uma coluna TINYINT(1)
- `date(string $name)`: Cria uma coluna DATE
- `dateTime(string $name)`: Cria uma coluna DATETIME
- `timestamp(string $name)`: Cria uma coluna TIMESTAMP
- `timestamps()`: Cria as colunas created_at e updated_at

## Modificadores de Coluna

Você pode modificar as colunas usando:

- `nullable()`: Permite valores NULL
- `default(string $value)`: Define um valor padrão
- `unique(string|array $columns)`: Cria um índice único
- `index(string|array $columns)`: Cria um índice normal

## Chaves Estrangeiras

Para criar chaves estrangeiras, use o método `foreign()`:

```php
$this->schema
    ->foreign('user_id')
    ->references('id')
    ->on('users')
    ->onDelete('CASCADE')
    ->onUpdate('CASCADE');
```

## Executando Migrações

Para executar uma migração:

```php
$migration = new CreateUsersTable();
$migration->run();
```

Para reverter uma migração:

```php
$migration = new CreateUsersTable();
$migration->rollback();
```

## Configuração do Banco de Dados

As configurações do banco de dados devem estar definidas no arquivo de configuração em `config/database.php`:

```php
return [
    'connection' => 'mysql',
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'seu_banco',
    'username' => 'seu_usuario',
    'password' => 'sua_senha'
];
``` 