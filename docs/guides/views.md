# Views

O sistema de views do FrameJam utiliza o Twig como template engine, oferecendo um sistema de templates poderoso e seguro.

## Estrutura de Diretórios

```
resources/
└── views/
    ├── layouts/
    │   └── app.twig
    ├── components/
    │   ├── header.twig
    │   └── footer.twig
    ├── pages/
    │   ├── home.twig
    │   └── about.twig
    └── errors/
        ├── 404.twig
        └── 500.twig
```

## Layouts

### Layout Base

```twig
{# resources/views/layouts/app.twig #}
<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}{% endblock %}</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    {% include 'components/header.twig' %}
    
    <main>
        {% block content %}{% endblock %}
    </main>
    
    {% include 'components/footer.twig' %}
    
    <script src="/js/app.js"></script>
</body>
</html>
```

### Página que Estende o Layout

```twig
{# resources/views/pages/home.twig #}
{% extends "layouts/app.twig" %}

{% block title %}Página Inicial{% endblock %}

{% block content %}
    <h1>Bem-vindo</h1>
    <p>Esta é a página inicial.</p>
{% endblock %}
```

## Variáveis e Dados

### Passando Dados

```php
// No Controller
public function index()
{
    return $this->view('pages/home.twig', [
        'title' => 'Página Inicial',
        'user' => $this->auth->user(),
        'posts' => Post::all()
    ]);
}
```

### Usando Dados no Template

```twig
<h1>{{ title }}</h1>

{% if user %}
    <p>Olá, {{ user.name }}!</p>
{% endif %}

<ul>
{% for post in posts %}
    <li>{{ post.title }}</li>
{% endfor %}
</ul>
```

## Componentes

### Header Component

```twig
{# resources/views/components/header.twig #}
<header>
    <nav>
        <a href="/">Home</a>
        <a href="/about">Sobre</a>
        {% if user %}
            <a href="/profile">Perfil</a>
            <a href="/logout">Sair</a>
        {% else %}
            <a href="/login">Entrar</a>
        {% endif %}
    </nav>
</header>
```

### Footer Component

```twig
{# resources/views/components/footer.twig #}
<footer>
    <p>&copy; {{ "now"|date("Y") }} Meu Site. Todos os direitos reservados.</p>
</footer>
```

## Filtros

### Filtros Básicos

```twig
{# Texto #}
{{ name|upper }}
{{ name|lower }}
{{ name|capitalize }}

{# Números #}
{{ price|number_format(2, ',', '.') }}
{{ count|abs }}

{# Datas #}
{{ date|date('d/m/Y') }}
{{ date|date('H:i:s') }}
```

### Filtros Personalizados

```php
// Registrar filtro
$twig->addFilter(new \Twig\TwigFilter('price', function ($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}));

// Uso no template
{{ product.price|price }}
```

## Funções

### Funções Básicas

```twig
{# URLs #}
<a href="{{ url('home') }}">Home</a>
<img src="{{ asset('images/logo.png') }}">

{# Formulários #}
<form action="{{ url('login') }}" method="POST">
    {{ csrf_field() }}
    <input type="text" name="email">
    <button type="submit">Entrar</button>
</form>
```

### Funções Personalizadas

```php
// Registrar função
$twig->addFunction(new \Twig\TwigFunction('active', function ($route) {
    return request()->is($route) ? 'active' : '';
}));

// Uso no template
<a href="{{ url('home') }}" class="{{ active('home') }}">Home</a>
```

## Macros

### Definindo Macros

```twig
{# resources/views/macros/forms.twig #}
{% macro input(name, value, type = 'text') %}
    <div class="form-group">
        <label for="{{ name }}">{{ name|capitalize }}</label>
        <input type="{{ type }}" name="{{ name }}" id="{{ name }}" value="{{ value }}">
    </div>
{% endmacro %}
```

### Usando Macros

```twig
{% import 'macros/forms.twig' as forms %}

<form action="{{ url('register') }}" method="POST">
    {{ forms.input('name') }}
    {{ forms.input('email', '', 'email') }}
    {{ forms.input('password', '', 'password') }}
    <button type="submit">Registrar</button>
</form>
```

## Exemplos Práticos

### Página de Blog

```twig
{# resources/views/pages/blog.twig #}
{% extends "layouts/app.twig" %}

{% block title %}Blog{% endblock %}

{% block content %}
    <div class="container">
        <h1>Blog</h1>
        
        <div class="posts">
            {% for post in posts %}
                <article class="post">
                    <h2>{{ post.title }}</h2>
                    <div class="meta">
                        Por {{ post.author.name }} em {{ post.created_at|date('d/m/Y') }}
                    </div>
                    <div class="content">
                        {{ post.content|truncate(200) }}
                    </div>
                    <a href="{{ url('posts.show', {id: post.id}) }}" class="read-more">
                        Ler mais
                    </a>
                </article>
            {% endfor %}
        </div>
        
        {% if posts.havePages() %}
            <div class="pagination">
                {{ posts.links()|raw }}
            </div>
        {% endif %}
    </div>
{% endblock %}
```

### Formulário de Contato

```twig
{# resources/views/pages/contact.twig #}
{% extends "layouts/app.twig" %}

{% block title %}Contato{% endblock %}

{% block content %}
    <div class="container">
        <h1>Entre em Contato</h1>
        
        {% if errors %}
            <div class="alert alert-danger">
                <ul>
                    {% for error in errors %}
                        <li>{{ error }}</li>
                    {% endfor %}
                </ul>
            </div>
        {% endif %}
        
        <form action="{{ url('contact') }}" method="POST">
            {{ csrf_field() }}
            
            <div class="form-group">
                <label for="name">Nome</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}">
            </div>
            
            <div class="form-group">
                <label for="message">Mensagem</label>
                <textarea name="message" id="message">{{ old('message') }}</textarea>
            </div>
            
            <button type="submit">Enviar</button>
        </form>
    </div>
{% endblock %}
```

## Boas Práticas

1. **Organização**
   - Mantenha templates organizados em diretórios lógicos
   - Use layouts para evitar duplicação de código
   - Crie componentes reutilizáveis

2. **Performance**
   - Use cache de templates em produção
   - Evite lógica complexa nos templates
   - Minimize o número de includes

3. **Segurança**
   - Escape dados automaticamente
   - Use CSRF protection em formulários
   - Valide dados no servidor

4. **Manutenção**
   - Documente macros e funções personalizadas
   - Use nomes descritivos
   - Mantenha a consistência no padrão de nomenclatura 