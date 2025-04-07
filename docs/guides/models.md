# Models

Os Models são a camada de acesso aos dados, fornecendo uma interface orientada a objetos para interagir com o banco de dados.

## Model Base

Todos os models devem estender a classe base `FrameJam\Core\Model`:

```php
use FrameJam\Core\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
}
```

## Configuração do Model

### Propriedades Básicas

```php
class User extends Model
{
    // Nome da tabela
    protected $table = 'users';
    
    // Campos que podem ser preenchidos em massa
    protected $fillable = ['name', 'email', 'password'];
    
    // Campos que devem ser ocultados na serialização
    protected $hidden = ['password', 'remember_token'];
    
    // Campos que devem ser convertidos para tipos específicos
    protected $casts = [
        'is_admin' => 'boolean',
        'settings' => 'array',
        'created_at' => 'datetime'
    ];
    
    // Relacionamentos
    protected $with = ['profile'];
}
```

## Operações Básicas

### Criar

```php
// Criar um registro
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// Criar múltiplos registros
$users = User::createMany([
    ['name' => 'John Doe'],
    ['name' => 'Jane Doe']
]);
```

### Ler

```php
// Buscar por ID
$user = User::find(1);

// Buscar todos
$users = User::all();

// Buscar com condições
$users = User::where('active', true)
    ->where('age', '>', 18)
    ->get();

// Buscar primeiro
$user = User::where('email', 'john@example.com')->first();
```

### Atualizar

```php
// Atualizar por ID
$user = User::find(1);
$user->update(['name' => 'New Name']);

// Atualizar com condições
User::where('active', false)
    ->update(['active' => true]);

// Atualizar em massa
User::whereIn('id', [1, 2, 3])
    ->update(['status' => 'inactive']);
```

### Deletar

```php
// Deletar por ID
$user = User::find(1);
$user->delete();

// Deletar com condições
User::where('active', false)->delete();

// Deletar em massa
User::whereIn('id', [1, 2, 3])->delete();
```

## Relacionamentos

### Um para Um

```php
class User extends Model
{
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}

class Profile extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### Um para Muitos

```php
class User extends Model
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}

class Post extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### Muitos para Muitos

```php
class User extends Model
{
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}

class Role extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
```

## Escopos

```php
class User extends Model
{
    // Escopo local
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    // Escopo com parâmetros
    public function scopeAge($query, $age)
    {
        return $query->where('age', '>=', $age);
    }
}

// Uso
$activeUsers = User::active()->get();
$adultUsers = User::age(18)->get();
```

## Eventos

```php
class User extends Model
{
    protected static function boot()
    {
        parent::boot();
        
        // Antes de criar
        static::creating(function ($user) {
            $user->password = Hash::make($user->password);
        });
        
        // Depois de criar
        static::created(function ($user) {
            // Enviar email de boas-vindas
        });
        
        // Antes de atualizar
        static::updating(function ($user) {
            // Lógica antes de atualizar
        });
        
        // Depois de deletar
        static::deleted(function ($user) {
            // Limpar dados relacionados
        });
    }
}
```

## Exemplos Práticos

### Model Completo

```php
namespace App\Models;

use FrameJam\Core\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'is_admin' => 'boolean',
        'settings' => 'array',
        'last_login' => 'datetime'
    ];
    
    // Relacionamentos
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    
    // Escopos
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }
    
    // Eventos
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            $user->password = Hash::make($user->password);
        });
        
        static::created(function ($user) {
            // Criar perfil padrão
            $user->profile()->create([
                'theme' => 'light',
                'notifications' => true
            ]);
        });
    }
    
    // Métodos personalizados
    public function isAdmin()
    {
        return $this->is_admin;
    }
    
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }
}
```

## Boas Práticas

1. **Organização**
   - Mantenha models focados em uma única entidade
   - Use relacionamentos para manter o código limpo
   - Separe a lógica de negócios dos models

2. **Performance**
   - Use eager loading para evitar N+1 queries
   - Indexe campos frequentemente consultados
   - Use cache quando apropriado

3. **Segurança**
   - Defina campos fillable e hidden
   - Valide dados antes de salvar
   - Use eventos para lógica sensível

4. **Manutenção**
   - Documente relacionamentos complexos
   - Use nomes descritivos
   - Mantenha a consistência no padrão de nomenclatura 