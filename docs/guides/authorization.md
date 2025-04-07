# Sistema de Autorização

O sistema de autorização do FrameJam fornece uma maneira simples e poderosa de gerenciar permissões e políticas de acesso em sua aplicação.

## Estrutura Básica

### Gates e Policies

```php
// Gates
Gate::define('update-post', function ($user, $post) {
    return $user->id === $post->user_id;
});

// Policies
class PostPolicy
{
    public function update(User $user, Post $post)
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post)
    {
        return $user->id === $post->user_id || $user->isAdmin();
    }
}
```

### Uso Básico

```php
// Verificar permissão
if (Gate::allows('update-post', $post)) {
    // ...
}

// Verificar negação
if (Gate::denies('update-post', $post)) {
    // ...
}

// Autorizar ação
$this->authorize('update', $post);

// Verificar para usuário específico
if (Gate::forUser($user)->allows('update-post', $post)) {
    // ...
}
```

## Exemplos Práticos

### Políticas de Recurso

```php
class PostPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Post $post)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->hasVerifiedEmail();
    }

    public function update(User $user, Post $post)
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post)
    {
        return $user->id === $post->user_id || $user->isAdmin();
    }

    public function restore(User $user, Post $post)
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Post $post)
    {
        return $user->isAdmin();
    }
}
```

### Gates para Funcionalidades

```php
class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Gate para gerenciar usuários
        Gate::define('manage-users', function ($user) {
            return $user->isAdmin();
        });

        // Gate para acessar configurações
        Gate::define('access-settings', function ($user) {
            return $user->hasRole('admin') || $user->hasRole('manager');
        });

        // Gate para moderação de conteúdo
        Gate::define('moderate-content', function ($user) {
            return $user->hasRole('moderator') || $user->hasRole('admin');
        });

        // Gate para relatórios
        Gate::define('view-reports', function ($user) {
            return $user->hasPermission('reports.view');
        });
    }
}
```

### Autorização em Controllers

```php
class PostController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Post::class, 'post');
    }

    public function index()
    {
        $posts = Post::paginate();
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $post = Post::create($request->validated());
        return redirect()->route('posts.show', $post);
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $post->update($request->validated());
        return redirect()->route('posts.show', $post);
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index');
    }
}
```

### Autorização em Blade

```php
@can('update', $post)
    <a href="{{ route('posts.edit', $post) }}" class="btn btn-primary">
        Editar
    </a>
@endcan

@cannot('update', $post)
    <p class="text-muted">Você não tem permissão para editar este post.</p>
@endcannot

@if(auth()->user()->can('delete', $post))
    <form action="{{ route('posts.destroy', $post) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Excluir</button>
    </form>
@endif
```

### Autorização em API

```php
class ApiController extends Controller
{
    public function update(Request $request, Post $post)
    {
        if (!auth()->user()->can('update', $post)) {
            return response()->json([
                'message' => 'Não autorizado'
            ], 403);
        }

        $post->update($request->validated());

        return response()->json([
            'message' => 'Post atualizado com sucesso',
            'data' => $post
        ]);
    }

    public function destroy(Post $post)
    {
        if (!auth()->user()->can('delete', $post)) {
            return response()->json([
                'message' => 'Não autorizado'
            ], 403);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post excluído com sucesso'
        ]);
    }
}
```

## Boas Práticas

1. **Organização**
   - Use políticas para recursos
   - Use gates para funcionalidades
   - Mantenha autorizações consistentes

2. **Performance**
   - Cache resultados de autorização
   - Otimize verificações
   - Use eager loading quando necessário

3. **Segurança**
   - Implemente princípio do menor privilégio
   - Valide todas as ações
   - Use middleware de autorização

4. **Manutenção**
   - Documente políticas
   - Mantenha roles atualizadas
   - Revise permissões periodicamente 