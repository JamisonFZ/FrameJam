# Sistema de Views do FrameJam

O FrameJam utiliza um sistema de views inspirado no Laravel Blade, oferecendo uma sintaxe simples e poderosa para criar templates.

## Estrutura de Diretórios

```
views/
├── layouts/          # Layouts base da aplicação
│   └── app.php      # Layout principal
├── components/      # Componentes reutilizáveis
└── *.php           # Views específicas
```

## Sintaxe Básica

### Layouts e Seções

1. **Definindo um Layout Base**
```php
// views/layouts/app.php
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Título Padrão')</title>
</head>
<body>
    @yield('content')
</body>
</html>
```

2. **Usando um Layout**
```php
// views/exemplo.php
@extends('layouts/app')

@section('title', 'Minha Página')

@section('content')
    <h1>Conteúdo da página</h1>
@endsection
```

### Controle de Fluxo

1. **Condicionais**
```php
@if($condicao)
    Conteúdo se verdadeiro
@endif

@if($condicao)
    Conteúdo se verdadeiro
@else
    Conteúdo se falso
@endif
```

2. **Loops**
```php
@foreach($items as $item)
    {{ $item->nome }}
@endforeach
```

### Inclusão de Arquivos

```php
@include('components/header')
```

### Variáveis e Expressões

1. **Exibindo Variáveis**
```php
{{ $variavel }}
```

2. **Expressões PHP**
```php
{{ date('Y') }}
{{ count($items) }}
```

## Passando Dados para as Views

No Controller:

```php
public function index()
{
    return $this->view('exemplo', [
        'titulo' => 'Meu Título',
        'items' => ['Item 1', 'Item 2']
    ]);
}
```

## Boas Práticas

1. **Organização**
   - Mantenha as views organizadas em diretórios lógicos
   - Use layouts para compartilhar código comum
   - Crie componentes reutilizáveis para elementos comuns

2. **Segurança**
   - Use `{{ }}` para escapar automaticamente o HTML
   - Evite usar PHP puro nas views
   - Mantenha a lógica de negócios nos controllers

3. **Performance**
   - Use layouts para evitar duplicação de código
   - Mantenha as views simples e focadas na apresentação
   - Evite loops aninhados complexos

## Exemplos Práticos

### Layout Completo
```php
// views/layouts/app.php
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'FrameJam')</title>
    @yield('styles')
</head>
<body>
    <header>
        @include('components/header')
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        @include('components/footer')
    </footer>

    @yield('scripts')
</body>
</html>
```

### View com Dados
```php
// views/produtos.php
@extends('layouts/app')

@section('title', 'Produtos')

@section('content')
    <h1>Lista de Produtos</h1>

    @if(count($produtos) > 0)
        <div class="grid">
            @foreach($produtos as $produto)
                <div class="card">
                    <h2>{{ $produto['nome'] }}</h2>
                    <p>{{ $produto['descricao'] }}</p>
                    <span>R$ {{ number_format($produto['preco'], 2, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
    @else
        <p>Nenhum produto encontrado.</p>
    @endif
@endsection
```

## Resolução de Problemas Comuns

1. **Layout não encontrado**
   - Verifique se o caminho do layout está correto
   - Certifique-se de que o arquivo existe no diretório correto

2. **Variáveis não definidas**
   - Verifique se as variáveis estão sendo passadas do controller
   - Use o operador de coalescência nula (??) para valores padrão

3. **Erros de sintaxe**
   - Verifique se todas as diretivas (@section, @endsection, etc.) estão corretamente fechadas
   - Certifique-se de que as aspas nos parâmetros estão corretas 