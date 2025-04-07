# FrameJam Framework

FrameJam é um framework PHP minimalista e extensível, projetado para desenvolvimento rápido de aplicações web e APIs.

## Índice

1. [Instalação](installation.md)
2. [Estrutura do Projeto](structure.md)
3. [Componentes Principais](components.md)
4. [Guias de Uso](guides/README.md)
5. [Referência da API](api/README.md)
6. [Exemplos](examples/README.md)
7. [Contribuição](contributing.md)

## Características Principais

- Sistema de rotas flexível
- Controllers MVC
- ORM com suporte a múltiplos bancos de dados
- Sistema de migrações
- API REST com autenticação
- Sistema de filas para processamento assíncrono
- Cache e sessão
- Middleware system
- Sistema de logs
- Internacionalização
- Upload seguro de arquivos
- Comandos CLI
- Testes automatizados

## Requisitos

- PHP 8.0 ou superior
- Composer
- Extensões PHP:
  - PDO
  - PDO_MYSQL
  - mbstring
  - json
  - fileinfo

## Instalação Rápida

```bash
composer create-project framejam/framejam my-project
cd my-project
php -S localhost:8000 -t public
```

## Estrutura de Diretórios

```
framejam/
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Views/
│   └── Jobs/
├── config/
├── database/
│   └── migrations/
├── public/
├── resources/
│   ├── lang/
│   └── views/
├── routes/
├── src/
│   └── Core/
├── storage/
│   ├── cache/
│   ├── logs/
│   └── uploads/
└── tests/
```

## Documentação Detalhada

Cada componente do framework possui sua própria documentação detalhada:

- [Roteamento](guides/routing.md)
- [Controllers](guides/controllers.md)
- [Models](guides/models.md)
- [Views](guides/views.md)
- [Database](guides/database.md)
- [API](guides/api.md)
- [Filas](guides/queues.md)
- [Cache](guides/cache.md)
- [Sessão](guides/session.md)
- [Middleware](guides/middleware.md)
- [Logs](guides/logging.md)
- [Internacionalização](guides/i18n.md)
- [Upload de Arquivos](guides/uploads.md)
- [CLI](guides/cli.md)
- [Testes](guides/testing.md)

## Exemplos

Veja exemplos práticos de uso do framework:

- [Blog](examples/blog.md)
- [API REST](examples/api.md)
- [Sistema de Filas](examples/queues.md)
- [Upload de Arquivos](examples/uploads.md)
- [Autenticação](examples/auth.md)

## Contribuição

Contribuições são bem-vindas! Veja nosso [guia de contribuição](contributing.md) para mais detalhes.

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE). 